<?php
session_start();
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'controllers/AuthController.php';
$userModel=new User($conn);
$auth=new AuthController($userModel);
$page=$_GET['page']??'home';
$error='';
$success='';
function h($v) {
    return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');
}
function logged() {
    return isset($_SESSION['user_id']);
}
function isAdmin() {
    return ($_SESSION['user_role']??'')==='admin';
}
function canManageUsers() {
    return in_array(currentRole(),['admin','hr'],true);
}
function canManageSchedules() {
    return in_array(currentRole(),['admin','dispatcher'],true);
}
function canViewReports() {
    return in_array(currentRole(),['admin','dispatcher','accounting','sales'],true);
}
function canPublishNotifications() {
    return in_array(currentRole(),['admin','hr','marketing'],true);
}
function isEmployee() {
    return in_array($_SESSION['user_role']??'', ['employee','admin','hr','dispatcher','technician','accounting','sales','customer_service','marketing'],true);
}
function currentRole() {
    $role=$_SESSION['user_role']??'customer';
    if($role==='employee') {
        $dept=strtolower(trim($_SESSION['department']??''));
        $map=['hr'=>'hr','dispatch'=>'dispatcher','it'=>'technician','accounting'=>'accounting','sales'=>'sales','customer service'=>'customer_service','marketing'=>'marketing','administration'=>'admin'];
        return $map[$dept]??'technician';
    }
    return $role;
}
function roleDashboard() {
    $r=currentRole();
    return in_array($r,['admin','hr','dispatcher','technician','accounting','sales','customer_service','marketing'],true)?$r:'dashboard';
}
function csrfToken() {
    if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verifyCsrf() {
    if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')) {
        http_response_code(403);
        die('Security token expired. Go back and try again.');
    }
}
$public=['home','products','services','about','contact','faq','register','login'];
$known=['home','products','services','about','contact','faq','register','login','logout','dashboard','request','checkin','admin','users','schedules','profile','history','support','support-desk','employee','reports','announcements','notifications','service-reports','settings','hr','dispatcher','technician','accounting','sales','customer_service','marketing'];
if(!in_array($page,$known,true))$page='home';
if($_SERVER['REQUEST_METHOD']==='POST')verifyCsrf();
if($page==='logout') {
    $auth->logout();
    header('Location: index.php');
    exit;
}
if($page==='register' && $_SERVER['REQUEST_METHOD']==='POST') {
    $r=$auth->register($_POST);
    $error=$r['error']??'';
    $success=$r['success']??'';
    if($success)$page='login';
}
if($page==='login' && $_SERVER['REQUEST_METHOD']==='POST') {
    $r=$auth->login($_POST);
    $error=$r['error']??'';
    if($r['success']??false) {
        header('Location: index.php?page=' . roleDashboard());
        exit;
    }
}
if(!in_array($page,$public,true) && !logged()) {
    header('Location: index.php?page=login');
    exit;
}
// Temporary staff passwords must be changed before accessing other account pages.
if(logged()) {
    $s=$conn->prepare('SELECT must_change_password FROM users WHERE id=? AND active=1');
    $s->bind_param('i',$_SESSION['user_id']);
    $s->execute();
    $account=$s->get_result()->fetch_assoc();
    $s->close();
    if(!$account) {
        $auth->logout();
        header('Location: index.php?page=login');
        exit;
    }
    $_SESSION['must_change_password']=(int)$account['must_change_password'];
    if($_SESSION['must_change_password']===1 && $page!=='profile') {
        $page='profile';
        $error='Change your temporary password to continue.';
    }
}
if($page==='admin' && !isAdmin()) {
    $page=roleDashboard();
    $error='Administrator access is required.';
}
if($page==='users' && !canManageUsers()) {
    $page=roleDashboard();
    $error='Only administrators and HR can manage staff accounts.';
}
if($page==='schedules' && !canManageSchedules()) {
    $page=roleDashboard();
    $error='Only administrators and dispatchers can manage schedules.';
}
if($page==='reports' && !canViewReports()) {
    $page=roleDashboard();
    $error='You do not have permission to view reports.';
}
if($page==='notifications' && !canPublishNotifications()) {
    $page=roleDashboard();
    $error='Only administrators, HR and marketing can publish announcements.';
}
if($page==='settings' && !isAdmin()) {
    $page=roleDashboard();
    $error='Only administrators can manage service settings.';
}
$rolePages=['hr'=>'hr','dispatcher'=>'dispatcher','technician'=>'technician','accounting'=>'accounting','sales'=>'sales','customer_service'=>'customer_service','marketing'=>'marketing'];
if(isset($rolePages[$page]) && currentRole()!==$page && !isAdmin()) {
    $page=roleDashboard();
    $error='You do not have permission to open that department dashboard.';
}
if(in_array($page,['checkin','employee','service-reports','announcements'],true)&&!isEmployee()) {
    $page=roleDashboard();
    $error='This page is available to employees only.';
}
if($page==='request' && ($_SESSION['user_role']??'')!=='customer') {
    $page='dashboard';
    $error='Only customer accounts can request services.';
}
if($page==='history' && ($_SESSION['user_role']??'')!=='customer') {
    $page=roleDashboard();
    $error='Service history is available to customer accounts.';
}
if($page==='profile' && $_SERVER['REQUEST_METHOD']==='POST') {
    if(isset($_POST['new_password'])) {
        /* password change is handled below */
    }
    elseif(!empty($_SESSION['must_change_password'])) {
        $error='Change your temporary password before updating profile details.';
    }
    elseif(($_POST['action']??'')==='save_preferences') {
        if(($_SESSION['user_role']??'')!=='customer') {
            $error='Notification preferences are available to customer accounts.';
        }
        else {
            $receiveReminders=isset($_POST['receive_reminders'])?1:0;
            $receivePromotions=isset($_POST['receive_promotions'])?1:0;
            $preferences=$conn->prepare('UPDATE users SET receive_reminders=?,receive_promotions=? WHERE id=?');
            $preferences->bind_param('iii',$receiveReminders,$receivePromotions,$_SESSION['user_id']);
            if($preferences->execute())$success='Notification preferences saved.';
            else$error='Could not save notification preferences.';
            $preferences->close();
        }
    }
    else {
        $first=trim($_POST['first_name']??'');
        $last=trim($_POST['last_name']??'');
        $middle=trim($_POST['middle_name']??'');
        $birth=$_POST['birthdate']?:null;
        $gender=trim($_POST['gender']??'');
        $email=trim($_POST['email']??'');
        $phone=trim($_POST['phone']??'');
        $address=trim($_POST['address']??'');
        if(!$first||!$last||!filter_var($email,FILTER_VALIDATE_EMAIL))$error='First name, last name, and a valid email are required.';
        else {
            $s=$conn->prepare('SELECT id FROM users WHERE email=? AND id<>?');
            $s->bind_param('si',$email,$_SESSION['user_id']);
            $s->execute();
            $dup=$s->get_result()->num_rows>0;
            $s->close();
            if($dup)$error='That email is already used by another account.';
            else {
                $s=$conn->prepare('UPDATE users SET first_name=?,last_name=?,middle_name=?,birthdate=?,gender=?,email=?,phone=?,address=? WHERE id=?');
                $s->bind_param('ssssssssi',$first,$last,$middle,$birth,$gender,$email,$phone,$address,$_SESSION['user_id']);
                if($s->execute()) {
                    $success='Profile updated.';
                    $_SESSION['user_name']=$first.' '.$last;
                }
                else$error='Unable to update profile.';
                $s->close();
            }
        }
    }
}
if($page==='profile' && isset($_POST['new_password'])) {
    $old=$_POST['current_password']??'';
    $new=$_POST['new_password']??'';
    $confirm=$_POST['confirm_password']??'';
    $s=$conn->prepare('SELECT password_hash FROM users WHERE id=?');
    $s->bind_param('i',$_SESSION['user_id']);
    $s->execute();
    $u=$s->get_result()->fetch_assoc();
    $s->close();
    if((empty($_SESSION['must_change_password']) && (!$u||!password_verify($old,$u['password_hash'])))||(!$u))$error='Current password is incorrect.';
    elseif(strlen($new)<8)$error='New password must be at least 8 characters.';
    elseif($new!==$confirm)$error='New passwords do not match.';
    else {
        $hash=password_hash($new,PASSWORD_DEFAULT);
        $s=$conn->prepare('UPDATE users SET password_hash=?,must_change_password=0 WHERE id=?');
        $s->bind_param('si',$hash,$_SESSION['user_id']);
        $s->execute();
        $s->close();
        $_SESSION['must_change_password']=0;
        $success='Password changed successfully.';
    }
}
if($page==='request' && $_SERVER['REQUEST_METHOD']==='POST') {
    $type=trim($_POST['service_type']??'');
    $date=$_POST['preferred_date']??'';
    $time=trim($_POST['preferred_time']??'')?:null;
    $notes=trim($_POST['notes']??'');
    if(!$type||!$date||$date<date('Y-m-d')) {
        $error='Choose a service and a valid future date.';
    }
    else {
        $serviceStatement=$conn->prepare('SELECT available_days,start_time,end_time FROM service_catalog WHERE name=? AND active=1');
        $serviceStatement->bind_param('s',$type);
        $serviceStatement->execute();
        $selectedService=$serviceStatement->get_result()->fetch_assoc();
        $serviceStatement->close();

        if(!$selectedService) {
            $error='Choose an available service.';
        }
        else {
            $weekday=date('l',strtotime($date));
            $availableDays=array_map('trim',explode(',',$selectedService['available_days']));
            $hasTimeRange=$selectedService['start_time']||$selectedService['end_time'];
            $timeValue=$time?substr($time,0,5):'';
            $startTime=$selectedService['start_time']?substr($selectedService['start_time'],0,5):'';
            $endTime=$selectedService['end_time']?substr($selectedService['end_time'],0,5):'';

            if(!in_array($weekday,$availableDays,true)) {
                $error='That service is not available on the selected day.';
            }
            elseif($hasTimeRange && !$time) {
                $error='Choose a time for this service.';
            }
            elseif($time && $startTime && $timeValue<$startTime) {
                $error='Choose a time after the service start time.';
            }
            elseif($time && $endTime && $timeValue>$endTime) {
                $error='Choose a time before the service end time.';
            }
            else {
                $statement=$conn->prepare('INSERT INTO service_requests(customer_id,service_type,preferred_date,preferred_time,notes) VALUES(?,?,?,?,?)');
                $statement->bind_param('issss',$_SESSION['user_id'],$type,$date,$time,$notes);
                if($statement->execute())$success='Service request submitted. You can track it in your dashboard.';
                else$error='Could not save request. Import the latest database.sql and try again.';
                $statement->close();
            }
        }
    }
}
if($page==='history' && $_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='cancel_request') {
    $id=(int)($_POST['request_id']??0);
    $s=$conn->prepare("UPDATE service_requests SET status='Cancelled' WHERE id=? AND customer_id=? AND status='Pending'");
    $s->bind_param('ii',$id,$_SESSION['user_id']);
    if($s->execute()&&$s->affected_rows)$success='Pending service request cancelled.';
    else$error='Only your pending requests can be cancelled.';
    $s->close();
}
if($page==='history' && $_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='update_request') {
    $id=(int)($_POST['request_id']??0);
    $date=trim($_POST['preferred_date']??'');
    $time=trim($_POST['preferred_time']??'')?:null;
    if(!$date||$date<date('Y-m-d')) {
        $error='Choose a valid future date.';
    }
    else {
        $requestCheck=$conn->prepare("SELECT service_type FROM service_requests WHERE id=? AND customer_id=? AND status='Pending'");
        $requestCheck->bind_param('ii',$id,$_SESSION['user_id']);
        $requestCheck->execute();
        $requestData=$requestCheck->get_result()->fetch_assoc();
        $requestCheck->close();

        if(!$requestData) {
            $error='Only your pending service requests can be changed.';
        }
        else {
            $serviceCheck=$conn->prepare('SELECT available_days,start_time,end_time FROM service_catalog WHERE name=?');
            $serviceCheck->bind_param('s',$requestData['service_type']);
            $serviceCheck->execute();
            $serviceData=$serviceCheck->get_result()->fetch_assoc();
            $serviceCheck->close();

            if($serviceData) {
                $weekday=date('l',strtotime($date));
                $availableDays=array_map('trim',explode(',',$serviceData['available_days']));
                $hasTimeRange=$serviceData['start_time']||$serviceData['end_time'];
                $timeValue=$time?substr($time,0,5):'';
                $startTime=$serviceData['start_time']?substr($serviceData['start_time'],0,5):'';
                $endTime=$serviceData['end_time']?substr($serviceData['end_time'],0,5):'';
                if(!in_array($weekday,$availableDays,true))$error='That service is not available on the selected day.';
                elseif($hasTimeRange&&!$time)$error='Choose a time for this service.';
                elseif($time&&$startTime&&$timeValue<$startTime)$error='Choose a time after the service start time.';
                elseif($time&&$endTime&&$timeValue>$endTime)$error='Choose a time before the service end time.';
            }

            if(!$error) {
                $update=$conn->prepare("UPDATE service_requests SET preferred_date=?,preferred_time=? WHERE id=? AND customer_id=? AND status='Pending'");
                $update->bind_param('ssii',$date,$time,$id,$_SESSION['user_id']);
                if($update->execute())$success='Your pending service request was updated.';
                else$error='Could not update your service request.';
                $update->close();
            }
        }
    }
}
if($page==='checkin' && $_SERVER['REQUEST_METHOD']==='POST' && isEmployee()) {
    $lat=($_POST['latitude']??'')!==''?filter_var($_POST['latitude'],FILTER_VALIDATE_FLOAT):null;
    $lon=($_POST['longitude']??'')!==''?filter_var($_POST['longitude'],FILTER_VALIDATE_FLOAT):null;
    $job=(int)($_POST['service_request_id']??0);
    if($lat!==null&&($lat===false||$lat < -90||$lat>90))$error='Invalid latitude.';
    elseif($lon!==null&&($lon===false||$lon < -180||$lon>180))$error='Invalid longitude.';
    else {
        $jobId=null;
        if($job>0) {
            $s=$conn->prepare('SELECT id FROM service_requests WHERE id=? AND assigned_technician_id=?');
            $s->bind_param('ii',$job,$_SESSION['user_id']);
            $s->execute();
            if($s->get_result()->num_rows)$jobId=$job;
            else$error='That job is not assigned to your account.';
            $s->close();
        }
        if(!$error) {
            $s=$conn->prepare('INSERT INTO checkins(employee_id,service_request_id,latitude,longitude) VALUES(?,?,?,?)');
            $s->bind_param('iidd',$_SESSION['user_id'],$jobId,$lat,$lon);
            if($s->execute())$success='Check-in recorded with server timestamp.';
            else$error='Unable to save check-in. Check the database schema.';
            $s->close();
        }
    }
}
if($page==='users' && $_SERVER['REQUEST_METHOD']==='POST' && canManageUsers()) {
    $action=$_POST['action']??'';
    if($action==='create_employee') {
        $f=trim($_POST['first_name']??'');
        $l=trim($_POST['last_name']??'');
        $m=trim($_POST['middle_name']??'');
        $birth=trim($_POST['birthdate']??'')?:null;
        $gender=trim($_POST['gender']??'');
        $email=trim($_POST['email']??'');
        $phone=trim($_POST['phone']??'');
        $address=trim($_POST['address']??'');
        $uname=trim($_POST['username']??'');
        $pw=$_POST['password']??'';
        $dept=trim($_POST['department']??'');
        $allowedDepartments=['Administration','IT','Dispatch','Accounting','HR','Marketing','Sales','Customer Service'];
        $allowedRoles=['admin','hr','dispatcher','technician','accounting','sales','customer_service','marketing'];
        $role=$_POST['role']??'technician';
        if(!in_array($dept,$allowedDepartments,true))$dept='IT';
        if(!in_array($role,$allowedRoles,true))$role='technician';
        if(!$f||!$l||!filter_var($email,FILTER_VALIDATE_EMAIL)||!preg_match('/^[a-zA-Z0-9_.-]{3,50}$/',$uname)||strlen($pw)<8)$error='Complete employee details; use a valid email, username and password of at least 8 characters.';
        elseif($userModel->accountExists($email,$uname))$error='Email or username already exists.';
        else {
            $hash=password_hash($pw,PASSWORD_DEFAULT);
            $s=$conn->prepare('INSERT INTO users(first_name,last_name,middle_name,birthdate,gender,email,phone,address,username,password_hash,role,department,must_change_password) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,1)');
            $s->bind_param('ssssssssssss',$f,$l,$m,$birth,$gender,$email,$phone,$address,$uname,$hash,$role,$dept);
            if($s->execute())$success='Employee account created. They must change the temporary password after logging in.';
            else$error='Could not create employee account.';
            $s->close();
        }
    }
    if($action==='toggle_user') {
        $id=(int)($_POST['user_id']??0);
        if($id===$_SESSION['user_id'])$error='You cannot deactivate your own account.';
        else {
            $s=$conn->prepare('UPDATE users SET active=IF(active=1,0,1) WHERE id=?');
            $s->bind_param('i',$id);
            $s->execute();
            $s->close();
            $success='Account status updated.';
        }
    }
    if($action==='update_user') {
        $userId=(int)($_POST['user_id']??0);
        $first=trim($_POST['first_name']??'');
        $last=trim($_POST['last_name']??'');
        $middle=trim($_POST['middle_name']??'');
        $birth=trim($_POST['birthdate']??'')?:null;
        $gender=trim($_POST['gender']??'');
        $email=trim($_POST['email']??'');
        $phone=trim($_POST['phone']??'');
        $address=trim($_POST['address']??'');
        $role=$_POST['role']??'customer';
        $department=trim($_POST['department']??'');
        $allowedDepartments=['Administration','IT','Dispatch','Accounting','HR','Marketing','Sales','Customer Service'];
        $allowedRoles=['customer','admin','hr','dispatcher','technician','accounting','sales','customer_service','marketing'];

        if(!$first||!$last||!filter_var($email,FILTER_VALIDATE_EMAIL)) {
            $error='Enter a first name, last name and valid email.';
        }
        elseif(!in_array($role,$allowedRoles,true)) {
            $error='Choose a valid account role.';
        }
        elseif($department!=='' && !in_array($department,$allowedDepartments,true)) {
            $error='Choose a valid department.';
        }
        elseif($userId===(int)$_SESSION['user_id'] && $role!=='admin') {
            $error='You cannot remove administrator access from your own account.';
        }
        else {
            $emailCheck=$conn->prepare('SELECT id FROM users WHERE email=? AND id<>?');
            $emailCheck->bind_param('si',$email,$userId);
            $emailCheck->execute();
            $emailIsUsed=$emailCheck->get_result()->num_rows>0;
            $emailCheck->close();

            if($emailIsUsed) {
                $error='That email is already used by another account.';
            }
            else {
                $update=$conn->prepare('UPDATE users SET first_name=?,last_name=?,middle_name=?,birthdate=?,gender=?,email=?,phone=?,address=?,role=?,department=? WHERE id=?');
                $update->bind_param('ssssssssssi',$first,$last,$middle,$birth,$gender,$email,$phone,$address,$role,$department,$userId);
                if($update->execute())$success='Account details updated.';
                else$error='Could not update account details.';
                $update->close();
            }
        }
    }
}
if($page==='schedules' && $_SERVER['REQUEST_METHOD']==='POST' && canManageSchedules()) {
    $id=(int)($_POST['request_id']??0);
    $status=$_POST['status']??'Pending';
    $date=trim($_POST['preferred_date']??'');
    $time=trim($_POST['preferred_time']??'')?:null;
    $allowed=['Pending','Approved','Declined','Completed','Cancelled'];
    $tech=($_POST['technician_id']??'')!==''?(int)$_POST['technician_id']:null;
    $note=trim($_POST['admin_notes']??'');
    if(!in_array($status,$allowed,true))$error='Invalid booking status.';
    elseif(!$date)$error='Choose an appointment date.';
    else {
        $bookingCheck=$conn->prepare('SELECT service_type,customer_id,preferred_date,preferred_time FROM service_requests WHERE id=?');
        $bookingCheck->bind_param('i',$id);
        $bookingCheck->execute();
        $booking=$bookingCheck->get_result()->fetch_assoc();
        $bookingCheck->close();

        if(!$booking)$error='Booking not found.';
        elseif($date<date('Y-m-d')&&$date!==$booking['preferred_date'])$error='Choose a future date when changing an appointment.';
        else {
            $serviceCheck=$conn->prepare('SELECT available_days,start_time,end_time FROM service_catalog WHERE name=?');
            $serviceCheck->bind_param('s',$booking['service_type']);
            $serviceCheck->execute();
            $serviceData=$serviceCheck->get_result()->fetch_assoc();
            $serviceCheck->close();

            $savedTime=$booking['preferred_time']?substr($booking['preferred_time'],0,5):null;
            $scheduleChanged=$date!==$booking['preferred_date']||$time!==$savedTime;
            if($serviceData&&$scheduleChanged) {
                $weekday=date('l',strtotime($date));
                $availableDays=array_map('trim',explode(',',$serviceData['available_days']));
                $hasTimeRange=$serviceData['start_time']||$serviceData['end_time'];
                $timeValue=$time?substr($time,0,5):'';
                $startTime=$serviceData['start_time']?substr($serviceData['start_time'],0,5):'';
                $endTime=$serviceData['end_time']?substr($serviceData['end_time'],0,5):'';
                if(!in_array($weekday,$availableDays,true))$error='That service is not available on the selected day.';
                elseif($hasTimeRange&&!$time)$error='Choose a time for this service.';
                elseif($time&&$startTime&&$timeValue<$startTime)$error='Choose a time after the service start time.';
                elseif($time&&$endTime&&$timeValue>$endTime)$error='Choose a time before the service end time.';
            }

            if(!$error&&$tech!==null) {
                $technicianCheck=$conn->prepare("SELECT id FROM users WHERE id=? AND active=1 AND (role='technician' OR (role='employee' AND department='IT'))");
                $technicianCheck->bind_param('i',$tech);
                $technicianCheck->execute();
                if(!$technicianCheck->get_result()->num_rows)$error='Choose an active technician account.';
                $technicianCheck->close();
            }

            if(!$error) {
                $s=$conn->prepare('UPDATE service_requests SET preferred_date=?,preferred_time=?,status=?,assigned_technician_id=?,admin_notes=? WHERE id=?');
                $s->bind_param('sssisi',$date,$time,$status,$tech,$note,$id);
                if($s->execute()) {
                    $success='Booking updated.';
                    $notificationTitle='Service request #' . $id . ' updated';
                    $notificationMessage='Your request status is ' . $status . '. The current appointment is ' . $date;
                    if($time)$notificationMessage.=' at ' . $time;
                    $notificationMessage.='.';
                    $notificationType='System update';
                    $audience='customers';
                    $createdBy=(int)$_SESSION['user_id'];
                    $notification=$conn->prepare('INSERT INTO notifications(title,message,audience,type,created_by) VALUES(?,?,?,?,?)');
                    $notification->bind_param('ssssi',$notificationTitle,$notificationMessage,$audience,$notificationType,$createdBy);
                    if($notification->execute()) {
                        $notificationId=$conn->insert_id;
                        $recipient=(int)$booking['customer_id'];
                        $link=$conn->prepare('INSERT INTO user_notifications(user_id,notification_id) VALUES(?,?)');
                        $link->bind_param('ii',$recipient,$notificationId);
                        $link->execute();
                        $link->close();
                    }
                    $notification->close();
                }
                else$error='Could not update booking.';
                $s->close();
            }
        }
    }
}
if($page==='notifications' && $_SERVER['REQUEST_METHOD']==='POST' && canPublishNotifications()) {
    $title=trim($_POST['title']??'');
    $msg=trim($_POST['message']??'');
    $aud=$_POST['audience']??'all';
    $type=$_POST['type']??'Announcement';
    $allowedTypes=['Reminder','Promotion','System update','Announcement'];
    if(!$title||!$msg||!in_array($aud,['all','customers','employees'],true)||!in_array($type,$allowedTypes,true))$error='Enter a title, message, type and audience.';
    else {
        $s=$conn->prepare('INSERT INTO notifications(title,message,audience,type,created_by) VALUES(?,?,?,?,?)');
        $s->bind_param('ssssi',$title,$msg,$aud,$type,$_SESSION['user_id']);
        if($s->execute())$success='Announcement published.';
        else$error='Could not publish announcement.';
        $s->close();
    }
}
if($page==='settings' && $_SERVER['REQUEST_METHOD']==='POST' && isAdmin()) {
    $action=$_POST['action']??'';

    if($action==='save_service') {
        $serviceId=(int)($_POST['service_id']??0);
        $name=trim($_POST['name']??'');
        $price=trim($_POST['base_price']??'');
        $days=trim($_POST['available_days']??'');
        $startTime=trim($_POST['start_time']??'')?:null;
        $endTime=trim($_POST['end_time']??'')?:null;

        if(!$name||!is_numeric($price)||(float)$price<0||!$days) {
            $error='Enter a service name, a price of zero or more, and available days.';
        }
        elseif($startTime && $endTime && $startTime>=$endTime) {
            $error='End time must be later than start time.';
        }
        else {
            if($serviceId>0) {
                $statement=$conn->prepare('UPDATE service_catalog SET name=?,base_price=?,available_days=?,start_time=?,end_time=? WHERE id=?');
                $statement->bind_param('sdsssi',$name,$price,$days,$startTime,$endTime,$serviceId);
            }
            else {
                $statement=$conn->prepare('INSERT INTO service_catalog(name,base_price,available_days,start_time,end_time) VALUES(?,?,?,?,?)');
                $statement->bind_param('sdsss',$name,$price,$days,$startTime,$endTime);
            }

            if($statement->execute())$success='Service settings saved.';
            else$error='Could not save service. The name may already be in use.';
            $statement->close();
        }
    }

    if($action==='toggle_service') {
        $serviceId=(int)($_POST['service_id']??0);
        $statement=$conn->prepare('UPDATE service_catalog SET active=IF(active=1,0,1) WHERE id=?');
        $statement->bind_param('i',$serviceId);
        if($statement->execute())$success='Service availability updated.';
        else$error='Could not update service availability.';
        $statement->close();
    }
}
if($page==='support' && ($_SESSION['user_role']??'')!=='customer') {
    $page=roleDashboard();
    $error='Customer accounts can submit support tickets here.';
}
if($page==='support' && $_SERVER['REQUEST_METHOD']==='POST') {
    $sub=trim($_POST['subject']??'');
    $msg=trim($_POST['message']??'');
    if(!$sub||!$msg)$error='Enter a subject and message.';
    else {
        $s=$conn->prepare('INSERT INTO support_tickets(customer_id,subject,message) VALUES(?,?,?)');
        $s->bind_param('iss',$_SESSION['user_id'],$sub,$msg);
        if($s->execute())$success='Support ticket submitted.';
        else$error='Could not submit ticket.';
        $s->close();
    }
}
if($page==='support-desk' && currentRole()!=='customer_service' && !isAdmin()) {
    $page=roleDashboard();
    $error='Customer service access is required.';
}
if($page==='support-desk' && $_SERVER['REQUEST_METHOD']==='POST' && (currentRole()==='customer_service'||isAdmin())) {
    $ticket=(int)($_POST['ticket_id']??0);
    $status=$_POST['status']??'';
    if(!in_array($status,['Open','In Progress','Closed'],true))$error='Choose a valid ticket status.';
    else {
        $s=$conn->prepare('UPDATE support_tickets SET status=? WHERE id=?');
        $s->bind_param('si',$status,$ticket);
        $s->execute();
        $s->close();
        $success='Support ticket updated.';
    }
}
if($page==='service-reports' && $_SERVER['REQUEST_METHOD']==='POST' && isEmployee()) {
    $job=($_POST['service_request_id']??'')!==''?(int)$_POST['service_request_id']:null;
    $type=trim($_POST['issue_type']??'');
    $desc=trim($_POST['description']??'');
    if(!$type||!$desc)$error='Choose an issue type and enter a description.';
    else {
        $valid=true;
        if($job) {
            $s=$conn->prepare('SELECT id FROM service_requests WHERE id=? AND assigned_technician_id=?');
            $s->bind_param('ii',$job,$_SESSION['user_id']);
            $s->execute();
            $valid=$s->get_result()->num_rows>0;
            $s->close();
        }
        if(!$valid)$error='That job is not assigned to your account.';
        else {
            $s=$conn->prepare('INSERT INTO service_reports(service_request_id,employee_id,issue_type,description) VALUES(?,?,?,?)');
            $s->bind_param('iiss',$job,$_SESSION['user_id'],$type,$desc);
            if($s->execute())$success='Service report sent to administrators.';
            else$error='Could not save report.';
            $s->close();
        }
    }
}
if($page==='dashboard' && logged())$page=roleDashboard();
include 'views/partials/header.php';
if($error)echo '<div class="message error">'.h($error).'</div>';
if($success)echo '<div class="message success">'.h($success).'</div>';
$viewMap=['hr'=>'role-dashboard','dispatcher'=>'role-dashboard','technician'=>'role-dashboard','accounting'=>'role-dashboard','sales'=>'role-dashboard','customer_service'=>'role-dashboard','marketing'=>'role-dashboard','home'=>'home','products'=>'products','services'=>'services','about'=>'about','contact'=>'contact','faq'=>'faq','register'=>'register','login'=>'login','dashboard'=>'dashboard','request'=>'request','checkin'=>'checkin','admin'=>'admin','users'=>'users','schedules'=>'schedules','profile'=>'profile','history'=>'history','support'=>'support','support-desk'=>'support-desk','employee'=>'employee','reports'=>'reports','announcements'=>'announcements','notifications'=>'notifications','service-reports'=>'service-reports','settings'=>'settings'];
include 'views/'.$viewMap[$page].'.php';
include 'views/partials/footer.php';
?>
