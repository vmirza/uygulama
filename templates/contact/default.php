<?php

$params = preg_split('/,/', PARAMS, 2);
if($params[1] == 'send'){
    // CAPTCHA CONTROL
    if(strtolower($_POST['captcha']) != strtolower(CAPTCHA)){
        $skip = true;
        $page->code     = 400;
        $page->status   = u::translate('Alert');
        $page->message  = u::translate('The security code is not correct');
        $page->fields   = array('captcha');
    }
    // NAME CONTROL
    elseif(!$_POST['name']){
        $skip = true;
        $page->code     = 400;
        $page->status   = u::translate('Alert');
        $page->message  = u::translate('Name field can not be blank!');
        $page->fields   = array('name');
    }
    // EMAIL ADDR CONTROL
    elseif(!preg_match('/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+\.[a-z0-9]{2,5}$/',strtolower($_POST['email']))){
        $skip = true;
        $page->code     = 400;
        $page->status   = u::translate('Alert');
        $page->message  = u::translate('Email address is not correct!');
        $page->fields   = array('email');
    }
    // MESSAGE CONTROL
    elseif(!$_POST['message']){
        $skip = true;
        $page->code     = 400;
        $page->status   = u::translate('Alert');
        $page->message  = u::translate('Message field can not be blank!');
        $page->fields   = array('message');
    }
    // SEND EMAIL
    if(!$skip){
        $page = u::get('data/'.$params[0].'/content');
        $sender = $page->recipient.' <'.$page->recipient.'>';
        $recipient = $page->recipient.' <'.$page->recipient.'>';
        $subject = sprintf(u::translate('%s Contact Form'), DOMAIN);
        $message = '<table border="0" cellspace="5" width="100%">'
            .'<tr><td>'.u::translate('Date').'</td><td>'.@date('M-d-Y H:i:s').'</td></tr>'
            .'<tr><td>'.u::translate('Name').'</td><td>'.$_POST['name'].'</td></tr>'
            .'<tr><td>'.u::translate('Email').'</td>'
                .'<td><a href="mailto:'.$_POST['email'].'">'.$_POST['email'].'</a></td></tr>'
            .'<tr><td>'.u::translate('Message').'</td><td>'.nl2br($_POST['message']).'</td></tr>'
            .'</table>';
        if(u::email($sender, $recipient, $subject, $message)){
            $page->code     = 200;
            $page->status   = u::translate('Successfuly');
            $page->message  = u::translate('Thanks, Your message successfuly sent.');
        } else {
            $page->code     = 600;
            $page->status   = u::translate('Error');
            $page->message  = u::translate('Please, try again later!');
        }
    }   
}
?>
