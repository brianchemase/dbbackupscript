<?php

$connection = mysqli_connect("localhost","brianani_brian","5131brian07","brianani_db");

$tables = array();
$result = mysqli_query($connection,"SHOW TABLES");
while($row = mysqli_fetch_row($result)){
  $tables[] = $row[0];
}

$return = '';
foreach($tables as $table){
  $result = mysqli_query($connection,"SELECT * FROM ".$table);
  $num_fields = mysqli_num_fields($result);
  $row2 = mysqli_fetch_row(mysqli_query($connection,"SHOW CREATE TABLE ".$table));
 $return .= "\n\n".$row2[1].";\n\n";
   
  for($i=0;$i<$num_fields;$i++){
    while($row = mysqli_fetch_row($result)){
      $return .= "INSERT INTO ".$table." VALUES(";
      for($j=0;$j<$num_fields;$j++){
        $row[$j] = addslashes($row[$j]);
        if(isset($row[$j])){ $return .= '"'.$row[$j].'"';}
        else{ $return .= '""';}
        if($j<$num_fields-1){ $return .= ',';}
      }
      $return .= ");\n";
    }
  }
  $return .= "\n\n\n";
}
$filename = date('Y-m-d H:i:sa')."backup.sql";
//save file
$handle = fopen('/home/brianani/public_html/backup_db/'.$filename,"w+");
fwrite($handle,$return);
fclose($handle);
 
$htmlContent = '
    <html>
    <head>
        <title>Database Backup</title>
    </head>
    <body>
        <h1>Database successfully backedup!</h1>
        <table cellspacing="0" style="border: 2px dashed #FB4314; width: 300px; height: 200px;">
             <p align="center">Skycorp Datatase Backup received</p>
             
            <tr>';
 
$htmlContent .= ' </tr>
        </table>
    </body>
    </html>';
                 
           ;


 require '/home/brianai/public_html/mailer/PHPMailerAutoload.php';

//Create a new PHPMailer instance
$mail = new PHPMailer;

//Ask for HTML-friendly debug output
$mail->Debugoutput = 'html';

//Set the hostname of the mail server
$mail->Host = 'smtp-mail.outlook.com';

$mail->Port = 587;

//Set the encryption system to use - ssl (deprecated) or tls
$mail->SMTPSecure = 'tls';

//Whether to use SMTP authentication
$mail->SMTPAuth = true;

//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = "briananikayi@outlook.com";

//Password to use for SMTP authentication
$mail->Password = "Brian1994";
$mail->IsHTML(true);

//Set who the message is to be sent from
$mail->setFrom('briananikayi@outlook.com', 'DB System Backup');

//Set an alternative reply-to address
//$mail->addReplyTo($from, $name);

//Set who the message is to be sent to
 $mail_receiver = "brianchemo@gmail.com";// $_POST['mail_receiver'];

$mail->addAddress($mail_receiver, "brianchemo@gmail.com");

// add attchment
$uploadfile = tempnam(sys_get_temp_dir(), hash('sha256',"/backup_db/".$filename));
$mail->addAttachment('/home/brianani/public_html/backup_db/'.$filename);
echo $filename;
$mail->addAttachment($uploadfile, 'My uploaded file');
   
//Set the subject line
$mail->Subject = "DB Backup System";


$mail->Body =  $htmlContent;


//send the message, check for errors
if (!$mail->send()) {
    
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
   echo "Succesfully Sent";
}

 

 
?>