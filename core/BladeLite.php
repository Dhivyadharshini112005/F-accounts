<?php
declare(strict_types=1);

class BladeLite {
    private string $views;

    public function __construct(string $views) { $this->views=rtrim($views,'/'); }

    public function render(string $name, array $data=[]): string {
        $file=$this->views.'/'.str_replace('.','/',$name).'.blade.php';
        if (!is_file($file)) throw new RuntimeException("View [$name] not found.");
        $template=file_get_contents($file);
        if (preg_match('/@extends\(\s*[\'"]([^\'"]+)[\'"]\s*\)/',$template,$m)) {
            $parent=$m[1];
            $template=preg_replace('/@extends\([^\)]*\)\s*/','',$template,1);
            $sections=[];
            if (preg_match_all('/@section\(\s*[\'"]([^\'"]+)[\'"]\s*\)(.*?)@endsection/s',$template,$ms,PREG_SET_ORDER)) {
                foreach($ms as $sec) $sections[$sec[1]]=$sec[2];
            }
            $parentFile=$this->views.'/'.str_replace('.','/',$parent).'.blade.php';
            $layout=file_get_contents($parentFile);
            $layout=preg_replace_callback('/@yield\(\s*[\'"]([^\'"]+)[\'"]\s*\)/',fn($x)=>$sections[$x[1]]??'', $layout);
            $template=$layout;
        }
        return $this->compile($template,$data);
    }

    private function replaceCallDirective(string $s,string $directive, callable $cb): string {
        $offset=0;
        while(($pos=strpos($s,'@'.$directive,$offset))!==false){
            $after=$pos+strlen($directive)+1;
            while($after<strlen($s) && ctype_space($s[$after])) $after++;
            if(($s[$after]??'')!=='('){$offset=$after;continue;}
            $depth=0;$quote=null;$escape=false;$end=null;
            for($i=$after;$i<strlen($s);$i++){
                $ch=$s[$i];
                if($quote!==null){
                    if($escape){$escape=false;continue;}
                    if($ch==='\\'){$escape=true;continue;}
                    if($ch===$quote)$quote=null;
                    continue;
                }
                if($ch==="'"||$ch==='"'){$quote=$ch;continue;}
                if($ch==='(')$depth++;
                elseif($ch===')'){
                    $depth--;
                    if($depth===0){$end=$i;break;}
                }
            }
            if($end===null)break;
            $expr=substr($s,$after+1,$end-$after-1);
            $replacement=$cb($expr);
            $s=substr_replace($s,$replacement,$pos,$end-$pos+1);
            $offset=$pos+strlen($replacement);
        }
        return $s;
    }

    private function compile(string $s,array $data): string {
        // Blade comments must be removed before echo conversion.
        $s=preg_replace('/\{\{--.*?--\}\}/s','',$s);

        // Raw echos first.
        $s=preg_replace_callback('/\{!!\s*(.*?)\s*!!\}/s',fn($m)=>'<?= '.$m[1].' ?>',$s);
        $s=preg_replace_callback('/\{\{\s*(.*?)\s*\}\}/s',fn($m)=>'<?= e('.$m[1].') ?>',$s);

        $s=preg_replace_callback('/@json\(\s*(.*?)\s*\)/s',fn($m)=>'<?= json_for_js('.$m[1].') ?>',$s);

        $s=str_replace(['@csrf'],['<?= csrf_field() ?>'],$s);
        $s=preg_replace_callback('/@method\(\s*[\'"]([^\'"]+)[\'"]\s*\)/',fn($m)=>'<?= method_field("'.$m[1].'") ?>',$s);

        // @php blocks are intentionally kept as PHP blocks.
        $s=preg_replace('/@php\s*/','<?php ',$s);
        $s=preg_replace('/@endphp\s*/',' ?>',$s);

        // Error blocks.
        $s=preg_replace_callback('/@error\(\s*[\'"]([^\'"]+)[\'"]\s*\)(.*?)@enderror/s',
            fn($m)=>'<?php if($errors->has("'.$m[1].'")): $message=$errors->first("'.$m[1].'"); ?>'.$m[2].'<?php endif; ?>',$s);

        $s=preg_replace('/@auth\b/','<?php if(Auth::check()): ?>',$s);
        $s=preg_replace('/@endauth\b/','<?php endif; ?>',$s);

        $loopId=0;
        // forelse
        while(preg_match('/@forelse\((.*?)\)(.*?)@empty(.*?)@endforelse/s',$s,$m,PREG_OFFSET_CAPTURE)){
            $expr=$m[1][0]; $body=$m[2][0]; $empty=$m[3][0]; $loopId++;
            $var='$__forelseEmpty'.$loopId;
            $rep='<?php '.$var.'=true; foreach('.$expr.'): '.$var.'=false; ?>'.$body.'<?php endforeach; if('.$var.'): ?>'.$empty.'<?php endif; ?>';
            $s=substr_replace($s,$rep,$m[0][1],strlen($m[0][0]));
        }

        $s=$this->replaceCallDirective($s,'foreach',fn($x)=>'<?php foreach('.$x.'): ?>');
        $s=preg_replace('/@endforeach\b/','<?php endforeach; ?>',$s);

        $s=$this->replaceCallDirective($s,'elseif',fn($x)=>'<?php elseif('.$x.'): ?>');
        $s=$this->replaceCallDirective($s,'if',fn($x)=>'<?php if('.$x.'): ?>');
        $s=preg_replace('/@else\b/','<?php else: ?>',$s);
        $s=preg_replace('/@endif\b/','<?php endif; ?>',$s);

        global $errors, $oldInput;
        extract(['errors'=>$errors ?? new ValidationErrors([]),'oldInput'=>$oldInput ?? []], EXTR_SKIP);
        extract($data, EXTR_SKIP);
        ob_start();
        try {
            eval('?>'.$s);
        } catch (Throwable $e) {
            ob_end_clean();
            throw new RuntimeException('View render error: '.$e->getMessage(),0,$e);
        }
        return ob_get_clean();
    }
}

function view(string $name,array $data=[]): void {
    global $blade;
    echo $blade->render($name,$data);
    exit;
}

class CollectionLite implements IteratorAggregate, Countable {
    private array $items;
    public function __construct(array $items=[]) { $this->items=array_values($items); }
    public function getIterator(): Traversable { return new ArrayIterator($this->items); }
    public function count(): int { return count($this->items); }
    public function max(string $key): mixed {
        if (!$this->items) return null;
        return max(array_map(fn($x)=>(float)($x->{$key} ?? 0),$this->items));
    }
    public function first(): mixed { return $this->items[0] ?? null; }
    public function toArray(): array { return $this->items; }
}
