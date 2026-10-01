<?php
declare(strict_types=1);

function rows_to_collection(array $rows): CollectionLite { return new CollectionLite($rows); }
function with_driver(array $rows, Database $db): array {
    foreach($rows as $r) {
        if (isset($r->driver_id) && $r->driver_id !== null) {
            $r->driver=$db->first("SELECT * FROM drivers WHERE id=?",[(int)$r->driver_id]);
        } else $r->driver=null;
    }
    return $rows;
}
function require_auth(): void { if (!Auth::check()) redirect_to(route('login')); }
function require_owner(): void { require_auth(); if (!Auth::isOwner()) { http_response_code(403); exit('403 Forbidden'); } }

function validate_required(array $required): array {
    $errors=[];
    foreach($required as $key=>$label) {
        if (trim((string)($_POST[$key] ?? ''))==='') $errors[$key]=["$label is required."];
    }
    return $errors;
}

function dashboard_action(): void {
    global $db;
    $totalIncome=(float)$db->scalar("SELECT COALESCE(SUM(amount),0) FROM incomes");
    $totalExpenseOnly=(float)$db->scalar("SELECT COALESCE(SUM(amount),0) FROM expenses");
    $totalAdvance=(float)$db->scalar("SELECT COALESCE(SUM(amount),0) FROM advances");
    $totalExpense=$totalExpenseOnly+$totalAdvance;
    $balance=$totalIncome-$totalExpense;
    $cashIncome=(float)$db->scalar("SELECT COALESCE(SUM(CASE WHEN fees_payment_mode='cash' THEN fees_amount ELSE 0 END),0)+COALESCE(SUM(CASE WHEN gst_payment_mode='cash' THEN gst_amount ELSE 0 END),0) FROM incomes");
    $accountIncome=(float)$db->scalar("SELECT COALESCE(SUM(CASE WHEN fees_payment_mode='account' THEN fees_amount ELSE 0 END),0)+COALESCE(SUM(CASE WHEN gst_payment_mode='account' THEN gst_amount ELSE 0 END),0) FROM incomes");
    $cashExpense=(float)$db->scalar("SELECT COALESCE(SUM(amount),0) FROM expenses WHERE payment_mode='cash'");
    $accountExpense=(float)$db->scalar("SELECT COALESCE(SUM(amount),0) FROM expenses WHERE payment_mode='account'");
    $cashAdvance=(float)$db->scalar("SELECT COALESCE(SUM(amount),0) FROM advances WHERE payment_mode='cash'");
    $accountAdvance=(float)$db->scalar("SELECT COALESCE(SUM(amount),0) FROM advances WHERE payment_mode='account'");
    $cashBalance=$cashIncome-$cashExpense-$cashAdvance;
    $accountBalance=$accountIncome-$accountExpense-$accountAdvance;
    $recentIncome=rows_to_collection(with_driver($db->all("SELECT * FROM incomes ORDER BY created_at DESC,id DESC LIMIT 5"),$db));
    $recentExpense=rows_to_collection($db->all("SELECT * FROM expenses ORDER BY created_at DESC,id DESC LIMIT 5"));
    $totalDrivers=(int)$db->scalar("SELECT COUNT(*) FROM drivers");

    $month=(string)($_GET['month'] ?? date('Y-m'));
    if (!preg_match('/^\d{4}-\d{2}$/',$month)) $month=date('Y-m');
    $monthDisplay=date('F Y',strtotime($month.'-01'));
    $availableRows=$db->all("SELECT DATE_FORMAT(income_date,'%b-%y') label, MAX(income_date) mx FROM incomes WHERE income_date IS NOT NULL GROUP BY DATE_FORMAT(income_date,'%b-%y') ORDER BY mx DESC");
    $availableMonths=new CollectionLite(array_map(fn($x)=>$x->label,$availableRows));
    $selectedMonth=$availableMonths->first() ?? date('M-y');
    $monthly=[
      'driver_payout'=>0,'driver_amt'=>0,'company_amt'=>0,'commission'=>0,'gst'=>0,
      'business_commission'=>0,'pending_amt'=>0,'amount_paid'=>0,'advance_amount'=>0,'toll_fee'=>0,'service_charge'=>0
    ];
    $monthlyTrend=[];
    for($i=5;$i>=0;$i--){
        $d=new DateTime('first day of this month');
        $d->modify("-{$i} months");
        $ym=$d->format('Y-m');
        $income=(float)$db->scalar("SELECT COALESCE(SUM(amount),0) FROM incomes WHERE DATE_FORMAT(income_date,'%Y-%m')=?",[$ym]);
        $expense=(float)$db->scalar("SELECT COALESCE(SUM(amount),0) FROM expenses WHERE DATE_FORMAT(expense_date,'%Y-%m')=?",[$ym]);
        $advance=(float)$db->scalar("SELECT COALESCE(SUM(amount),0) FROM advances WHERE DATE_FORMAT(advance_date,'%Y-%m')=?",[$ym]);
        $monthlyTrend[]=['label'=>$d->format('M'),'income'=>$income,'expense'=>$expense+$advance];
    }
    $trendMax=1;
    foreach($monthlyTrend as $t) $trendMax=max($trendMax,(float)$t['income'],(float)$t['expense']);
    view('dashboard.index',compact('totalIncome','totalExpense','totalExpenseOnly','totalAdvance','balance','totalDrivers','cashIncome','accountIncome','cashExpense','accountExpense','cashAdvance','accountAdvance','cashBalance','accountBalance','recentIncome','recentExpense','month','monthDisplay','availableMonths','selectedMonth','monthly','monthlyTrend','trendMax'));
}

function login_action(): void {
    if ($_SERVER['REQUEST_METHOD']==='GET') view('auth.login');
    verify_csrf();
    $errors=validate_required(['username'=>'Username','password'=>'Password']);
    if($errors) fail_validation($errors);
    $user=$GLOBALS['db']->first("SELECT * FROM users WHERE username=? LIMIT 1",[(string)$_POST['username']]);
    if(!$user || !password_verify((string)$_POST['password'],(string)$user->password)){
        flash('error','Invalid username or password'); set_old($_POST); back();
    }
    Auth::login($user); flash('success','Welcome back, '.($user->name??$user->username).'.'); redirect_to(route('dashboard'));
}
function logout_action(): void { Auth::logout(); redirect_to(route('login')); }

function drivers_create(): void { require_auth(); view('drivers.create'); }
function drivers_store(): void {
    require_auth(); verify_csrf();
    $errors=validate_required(['name'=>'Name','status'=>'Status']);
    if($errors) fail_validation($errors);
    $id=$GLOBALS['db']->insert("INSERT INTO drivers (id_number,name,phone,vehicle_number,vehicle_name,pending_amount,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,NOW(),NOW())",[
      value_or_null($_POST['id_number']??null),trim((string)$_POST['name']),value_or_null($_POST['phone']??null),value_or_null($_POST['vehicle_number']??null),value_or_null($_POST['vehicle_name']??null),(float)($_POST['pending_amount']??0),$_POST['status']]);
    flash('success','Driver added successfully.'); redirect_to(route('dashboard'));
}
function drivers_edit(int $id): void { require_auth(); $d=$GLOBALS['db']->first("SELECT * FROM drivers WHERE id=?",[$id]); if(!$d){http_response_code(404);exit('Driver not found');} view('drivers.edit',['driver'=>$d]); }
function drivers_update(int $id): void {
    require_auth(); verify_csrf();
    $errors=validate_required(['name'=>'Name','status'=>'Status']); if($errors) fail_validation($errors);
    $GLOBALS['db']->execute("UPDATE drivers SET id_number=?,name=?,phone=?,vehicle_number=?,vehicle_name=?,pending_amount=?,status=?,updated_at=NOW() WHERE id=?",[
      value_or_null($_POST['id_number']??null),trim((string)$_POST['name']),value_or_null($_POST['phone']??null),value_or_null($_POST['vehicle_number']??null),value_or_null($_POST['vehicle_name']??null),(float)($_POST['pending_amount']??0),$_POST['status'],$id]);
    flash('success','Driver updated successfully.'); redirect_to(route('dashboard'));
}
function drivers_delete(int $id): void { require_owner(); verify_csrf(); $GLOBALS['db']->execute("DELETE FROM drivers WHERE id=?",[$id]); flash('success','Driver deleted successfully.'); redirect_to(route('dashboard')); }
function drivers_ledger(int $id): void {
    require_auth(); $driver=$GLOBALS['db']->first("SELECT * FROM drivers WHERE id=?",[$id]); if(!$driver){http_response_code(404);exit('Driver not found');}
    $incomes=rows_to_collection($GLOBALS['db']->all("SELECT * FROM incomes WHERE driver_id=? ORDER BY id DESC",[$id]));
    $totalPaid=(float)$GLOBALS['db']->scalar("SELECT COALESCE(SUM(amount),0) FROM incomes WHERE driver_id=?",[$id]);
    view('drivers.ledger',compact('driver','incomes','totalPaid'));
}
function drivers_payment(int $id): void {
    require_auth(); verify_csrf();
    $driver=$GLOBALS['db']->first("SELECT * FROM drivers WHERE id=?",[$id]); if(!$driver){http_response_code(404);exit('Driver not found');}
    $amount=(float)($_POST['amount']??0); $pending=(float)($driver->pending_amount??0);
    if($amount<=0){flash('error','Payment amount must be greater than zero.');back();}
    if($pending<=0){flash('error','There is no pending amount for this driver.');back();}
    if($amount>$pending){flash('error','Payment cannot be greater than the current pending amount of ₹'.number_format($pending,2));back();}
    $date=$_POST['payment_date']??now_date();
    $GLOBALS['db']->insert("INSERT INTO incomes (driver_id,fees_amount,gst_amount,amount,total_amount,fees_payment_mode,gst_payment_mode,income_date,description,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,NOW(),NOW())",[$id,$amount,0,$amount,$amount,'cash','cash',$date,value_or_null($_POST['description']??'Driver payment')]);
    $GLOBALS['db']->execute("UPDATE drivers SET pending_amount=?,updated_at=NOW() WHERE id=?", [round(max(0,$pending-$amount),2),$id]);
    flash('success','Payment recorded successfully.'); redirect_to(route('drivers.ledger',$id));
}

function income_index(): void {
    require_auth(); $search=trim((string)($_GET['search']??''));
    $sql="SELECT * FROM incomes"; $params=[];
    if($search!==''){ $sql.=" WHERE (driver_id LIKE ? OR description LIKE ? OR fees_amount LIKE ? OR gst_amount LIKE ? OR total_amount LIKE ? OR amount LIKE ? OR income_date LIKE ?)"; $like="%$search%"; $params=array_fill(0,7,$like); }
    $sql.=" ORDER BY income_date DESC,id DESC";
    $rows=with_driver($GLOBALS['db']->all($sql,$params),$GLOBALS['db']); view('income.index',['incomes'=>rows_to_collection($rows)]);
}
function income_create(): void { require_auth(); $drivers=rows_to_collection($GLOBALS['db']->all("SELECT * FROM drivers ORDER BY name")); view('income.create',compact('drivers')); }
function income_store(): void {
    require_auth(); verify_csrf();
    $errors=validate_required(['driver_id'=>'Driver','fees_amount'=>'Fees amount','fees_payment_mode'=>'Fees payment mode','gst_payment_mode'=>'GST payment mode','income_date'=>'Income date']);
    if($errors) fail_validation($errors);
    if(!in_array($_POST['fees_payment_mode'],['cash','account'],true)||!in_array($_POST['gst_payment_mode'],['cash','account'],true)) fail_validation(['payment_mode'=>['Invalid payment mode.']]);
    $fees=(float)$_POST['fees_amount']; $gst=(float)($_POST['gst_amount']??0); $total=$fees+$gst;
    $GLOBALS['db']->insert("INSERT INTO incomes (driver_id,fees_amount,gst_amount,amount,total_amount,fees_payment_mode,gst_payment_mode,fees_upi_id,gst_upi_id,income_date,description,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())",[
      (int)$_POST['driver_id'],$fees,$gst,$total,$total,$_POST['fees_payment_mode'],$_POST['gst_payment_mode'],value_or_null($_POST['fees_upi_id']??null),value_or_null($_POST['gst_upi_id']??null),$_POST['income_date'],value_or_null($_POST['description']??null)]);
    flash('success','Income added successfully.'); redirect_to(route('income.index'));
}
function income_edit(int $id): void { require_owner(); $income=$GLOBALS['db']->first("SELECT * FROM incomes WHERE id=?",[$id]); if(!$income){http_response_code(404);exit('Income not found');} $drivers=rows_to_collection($GLOBALS['db']->all("SELECT * FROM drivers ORDER BY name")); view('income.edit',compact('income','drivers')); }
function income_update(int $id): void {
    require_owner(); verify_csrf(); $errors=validate_required(['driver_id'=>'Driver','fees_amount'=>'Fees amount','fees_payment_mode'=>'Fees payment mode','gst_payment_mode'=>'GST payment mode','income_date'=>'Income date']); if($errors) fail_validation($errors);
    $fees=(float)$_POST['fees_amount'];$gst=(float)($_POST['gst_amount']??0);$total=$fees+$gst;
    $GLOBALS['db']->execute("UPDATE incomes SET driver_id=?,fees_amount=?,gst_amount=?,amount=?,total_amount=?,fees_payment_mode=?,gst_payment_mode=?,fees_upi_id=?,gst_upi_id=?,income_date=?,description=?,updated_at=NOW() WHERE id=?",[(int)$_POST['driver_id'],$fees,$gst,$total,$total,$_POST['fees_payment_mode'],$_POST['gst_payment_mode'],value_or_null($_POST['fees_upi_id']??null),value_or_null($_POST['gst_upi_id']??null),$_POST['income_date'],value_or_null($_POST['description']??null),$id]);
    flash('success','Income updated successfully.'); redirect_to(route('income.index'));
}
function income_delete(int $id): void { require_owner(); verify_csrf(); $GLOBALS['db']->execute("DELETE FROM incomes WHERE id=?",[$id]); flash('success','Income deleted successfully.'); redirect_to(route('income.index')); }

function expense_index(): void {
    require_auth(); $search=trim((string)($_GET['search']??'')); $sql="SELECT * FROM expenses";$params=[];
    if($search!==''){ $like="%$search%";$sql.=" WHERE (expense_name LIKE ? OR expense_type LIKE ? OR category LIKE ? OR description LIKE ? OR amount LIKE ? OR payment_mode LIKE ? OR expense_date LIKE ?)";$params=array_fill(0,7,$like);}
    $sql.=" ORDER BY expense_date DESC,id DESC"; view('expenses.index',['expenses'=>rows_to_collection($GLOBALS['db']->all($sql,$params))]);
}
function expense_create(): void { require_auth(); view('expenses.create'); }
function expense_store(): void {
    require_auth();verify_csrf();$errors=validate_required(['expense_name'=>'Expense name','amount'=>'Amount','payment_mode'=>'Payment mode','expense_date'=>'Expense date']);if($errors)fail_validation($errors);
    $category=value_or_null($_POST['category']??null);
    $GLOBALS['db']->insert("INSERT INTO expenses (expense_name,expense_type,amount,payment_mode,upi_id,date,expense_date,category,description,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,NOW(),NOW())",[
      trim((string)$_POST['expense_name']),(float)$_POST['amount'],$_POST['payment_mode'],value_or_null($_POST['upi_id']??null),$_POST['expense_date'],$_POST['expense_date'],$category,value_or_null($_POST['description']??null)]);
    flash('success','Expense added successfully.');redirect_to(route('expenses.index'));
}
function expense_edit(int $id): void { require_owner();$expense=$GLOBALS['db']->first("SELECT * FROM expenses WHERE id=?",[$id]);if(!$expense){http_response_code(404);exit('Expense not found');}view('expenses.edit',compact('expense')); }
function expense_update(int $id): void {
    require_owner();verify_csrf();$errors=validate_required(['expense_name'=>'Expense name','amount'=>'Amount','payment_mode'=>'Payment mode','expense_date'=>'Expense date']);if($errors)fail_validation($errors);
    $category=value_or_null($_POST['category']??null);$type=$category??trim((string)$_POST['expense_name']);
    $GLOBALS['db']->execute("UPDATE expenses SET expense_name=?,expense_type=?,amount=?,payment_mode=?,upi_id=?,date=?,expense_date=?,category=?,description=?,updated_at=NOW() WHERE id=?",[
      trim((string)$_POST['expense_name']),$type,(float)$_POST['amount'],$_POST['payment_mode'],value_or_null($_POST['upi_id']??null),$_POST['expense_date'],$_POST['expense_date'],$category,value_or_null($_POST['description']??null),$id]);
    flash('success','Expense updated successfully.');redirect_to(route('expenses.index'));
}
function expense_delete(int $id): void { require_owner();verify_csrf();$GLOBALS['db']->execute("DELETE FROM expenses WHERE id=?",[$id]);flash('success','Expense deleted successfully.');redirect_to(route('expenses.index')); }
function expense_voucher(int $id): void { require_auth();$expense=$GLOBALS['db']->first("SELECT * FROM expenses WHERE id=?",[$id]);if(!$expense){http_response_code(404);exit('Expense not found');}$n=(int)$GLOBALS['db']->scalar("SELECT COUNT(*) FROM expenses WHERE id<=?",[$id]);$displayVoucherNumber=str_pad((string)$n,3,'0',STR_PAD_LEFT);$amountInWords=numberToWords((int)floor((float)$expense->amount));view('expenses.voucher',compact('expense','displayVoucherNumber','amountInWords'));}

function advance_index(): void {
    require_auth();$search=trim((string)($_GET['search']??''));$sql="SELECT * FROM advances";$params=[];
    if($search!==''){ $like="%$search%";$sql.=" WHERE (employee_name LIKE ? OR category LIKE ? OR amount LIKE ? OR payment_mode LIKE ? OR upi_id LIKE ? OR description LIKE ? OR advance_date LIKE ?)";$params=array_fill(0,7,$like);}
    $sql.=" ORDER BY advance_date DESC,id DESC";view('advances.index',['advances'=>rows_to_collection($GLOBALS['db']->all($sql,$params))]);
}
function advance_create(): void { require_auth();view('advances.create'); }
function advance_store(): void {
    require_auth();verify_csrf();$errors=validate_required(['employee_name'=>'Employee name','amount'=>'Amount','payment_mode'=>'Payment mode','advance_date'=>'Advance date']);if($errors)fail_validation($errors);
    $GLOBALS['db']->insert("INSERT INTO advances (employee_name,category,amount,payment_mode,upi_id,advance_date,description,created_at,updated_at) VALUES (?,?,?,?,?,?,?,NOW(),NOW())",[trim((string)$_POST['employee_name']),'Salary Advance',(float)$_POST['amount'],$_POST['payment_mode'],value_or_null($_POST['upi_id']??null),$_POST['advance_date'],value_or_null($_POST['description']??null)]);
    flash('success','Salary advance added successfully.');redirect_to(route('expenses.advances.index'));
}
function advance_edit(int $id): void { require_owner();$advance=$GLOBALS['db']->first("SELECT * FROM advances WHERE id=?",[$id]);if(!$advance){http_response_code(404);exit('Advance not found');}view('advances.edit',compact('advance')); }
function advance_update(int $id): void {
    require_owner();verify_csrf();$errors=validate_required(['employee_name'=>'Employee name','amount'=>'Amount','payment_mode'=>'Payment mode','advance_date'=>'Advance date']);if($errors)fail_validation($errors);
    $GLOBALS['db']->execute("UPDATE advances SET employee_name=?,category='Salary Advance',amount=?,payment_mode=?,upi_id=?,advance_date=?,description=?,updated_at=NOW() WHERE id=?",[trim((string)$_POST['employee_name']),(float)$_POST['amount'],$_POST['payment_mode'],value_or_null($_POST['upi_id']??null),$_POST['advance_date'],value_or_null($_POST['description']??null),$id]);
    flash('success','Salary advance updated successfully.');redirect_to(route('expenses.advances.index'));
}
function advance_delete(int $id): void { require_owner();verify_csrf();$GLOBALS['db']->execute("DELETE FROM advances WHERE id=?",[$id]);flash('success','Salary advance deleted successfully.');redirect_to(route('expenses.advances.index')); }
function advance_voucher(int $id): void { require_auth();$advance=$GLOBALS['db']->first("SELECT * FROM advances WHERE id=?",[$id]);if(!$advance){http_response_code(404);exit('Advance not found');}$n=(int)$GLOBALS['db']->scalar("SELECT COUNT(*) FROM advances WHERE id<=?",[$id]);$displayVoucherNumber=str_pad((string)$n,3,'0',STR_PAD_LEFT);$amountInWords=numberToWords((int)floor((float)$advance->amount));view('advances.voucher',compact('advance','displayVoucherNumber','amountInWords'));}

function report_index(): void {
    require_auth();$period=$_GET['period']??'all';if(!in_array($period,['all','month','previous_month','year','custom'],true))$period='all';
    $from=$_GET['from_date']??null;$to=$_GET['to_date']??null;$start=$end=null;
    if($period==='month'){$start=date('Y-m-01');$end=date('Y-m-t');}
    elseif($period==='previous_month'){$ts=strtotime('first day of last month');$start=date('Y-m-01',$ts);$end=date('Y-m-t',$ts);}
    elseif($period==='year'){$start=date('Y-01-01');$end=date('Y-12-31');}
    elseif($period==='custom'&&$from&&$to){if($from>$to)[$from,$to]=[$to,$from];$start=$from;$end=$to;}
    $iq="SELECT i.*,d.name driver_name,d.vehicle_number FROM incomes i LEFT JOIN drivers d ON d.id=i.driver_id";$eq="SELECT * FROM expenses";$ip=$ep=[];
    if($start&&$end){$iq.=" WHERE i.income_date BETWEEN ? AND ?";$ip=[$start,$end];$eq.=" WHERE expense_date BETWEEN ? AND ?";$ep=[$start,$end];}
    $iq.=" ORDER BY i.income_date ASC,i.id ASC";$eq.=" ORDER BY expense_date ASC,id ASC";
    $incomes=rows_to_collection(with_driver($GLOBALS['db']->all($iq,$ip),$GLOBALS['db']));$expenses=rows_to_collection($GLOBALS['db']->all($eq,$ep));
    if(($_GET['export']??'')==='excel'){
        $name='financial_report_'.date('Ymd_His').'.csv';header('Content-Type:text/csv; charset=UTF-8');header('Content-Disposition:attachment; filename="'.$name.'"');
        $h=fopen('php://output','w');fprintf($h,chr(0xEF).chr(0xBB).chr(0xBF));fputcsv($h,['FINANCIAL REPORT']);fputcsv($h,[]);fputcsv($h,['INCOME RECORDS']);fputcsv($h,['Date','Driver','Vehicle','Description','Amount']);
        foreach($incomes as $i)fputcsv($h,[$i->income_date,$i->driver_name??'-',$i->vehicle_number??'-',$i->description??'-',number_format((float)$i->amount,2,'.','')]);
        fputcsv($h,[]);fputcsv($h,['EXPENSE RECORDS']);fputcsv($h,['Date','Category','Description','Amount']);
        foreach($expenses as $x)fputcsv($h,[$x->expense_date,$x->category??'Uncategorized',$x->description??'-',number_format((float)$x->amount,2,'.','')]);fclose($h);exit;
    }
    $totalIncome=array_sum(array_map(fn($x)=>(float)$x->amount,$incomes->toArray()));$totalExpense=array_sum(array_map(fn($x)=>(float)$x->amount,$expenses->toArray()));$balance=$totalIncome-$totalExpense;
    $cat=[];foreach($expenses as $x){$k=$x->category?:'Uncategorized';$cat[$k]=($cat[$k]??0)+(float)$x->amount;}arsort($cat);$expenseCategories=$cat;
    $reportTitle=$period==='month'?date('F Y'):($period==='previous_month'?date('F Y',strtotime('last month')):($period==='year'?date('Y'):($period==='custom'&&$from&&$to?date('d-m-Y',strtotime($from)).' to '.date('d-m-Y',strtotime($to)):'All Time')));
    $fromDate=$from; $toDate=$to; view('reports.index',compact('incomes','expenses','totalIncome','totalExpense','balance','fromDate','toDate','period','reportTitle','expenseCategories'));
}
function search_index(): void {
    require_auth();$search=trim((string)($_GET['search']??''));$like="%$search%";
    $ip=[];$ep=[];$iq="SELECT i.*,d.name driver_name,d.vehicle_number FROM incomes i LEFT JOIN drivers d ON d.id=i.driver_id";$eq="SELECT * FROM expenses";
    if($search!==''){$iq.=" WHERE (i.driver_id LIKE ? OR i.description LIKE ? OR i.fees_amount LIKE ? OR i.gst_amount LIKE ? OR i.total_amount LIKE ? OR i.amount LIKE ? OR i.income_date LIKE ?)";$ip=array_fill(0,7,$like);$eq.=" WHERE (description LIKE ? OR amount LIKE ? OR expense_date LIKE ? OR category LIKE ? OR expense_name LIKE ?)";$ep=array_fill(0,5,$like);}
    $iq.=" ORDER BY i.income_date DESC,i.id DESC";$eq.=" ORDER BY expense_date DESC,id DESC";
    $incomes=rows_to_collection(with_driver($GLOBALS['db']->all($iq,$ip),$GLOBALS['db']));$expenses=rows_to_collection($GLOBALS['db']->all($eq,$ep));view('search.search',compact('search','incomes','expenses'));
}

function daily_account(): void {
    require_auth();
    $incomes=rows_to_collection(with_driver($GLOBALS['db']->all("SELECT * FROM incomes ORDER BY income_date DESC,id DESC"),$GLOBALS['db']));
    $expenses=rows_to_collection($GLOBALS['db']->all("SELECT * FROM expenses ORDER BY expense_date DESC,id DESC"));
    $totalIncome=(float)$GLOBALS['db']->scalar("SELECT COALESCE(SUM(amount),0) FROM incomes");$totalExpense=(float)$GLOBALS['db']->scalar("SELECT COALESCE(SUM(amount),0) FROM expenses");$balance=$totalIncome-$totalExpense;
    view('reports.index',compact('incomes','expenses','totalIncome','totalExpense','balance'));
}
