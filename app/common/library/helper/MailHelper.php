<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowing一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------
namespace app\common\library\helper;
use app\common\model\Users;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class MailHelper
{
    /**
     * 获取邮件模板
     * @return false|string
     */
    public static function getTemplate($template)
    {
        $handle = fopen($template, "r");//读取二进制文件时，需要将第二个参数设置成'rb'
        //通过filesize获得文件大小，将整个文件一下子读到一个字符串中
        $contents = fread($handle, filesize ($template));
        fclose($handle);
        return $contents;
    }

    /**
	 * 验证是否是邮箱
	 * @param $email
	 * @return bool
	 */
	public static function isEmail($email): bool
    {
		if (filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			return true;
		}
		return false;
	}

    /**
     * 发送邮件
     * @param $email
     * @param $email_type
     * @param array $data
     * @return array
     */
    public static function sendEmail($email, $email_type, array $data=[]): array
    {
        $emailConfig = get_setting();
        $mail = new PHPMailer(true);

        if(!$emailConfig['email_enable'] || !$emailConfig['email_host'] || !$emailConfig['email_username'] || !$emailConfig['email_password'])
        {
            return ['code'=>0,'message'=>'邮件功能未启用或配置不完整'];
        }

        if(isset($email) && !MailHelper::isEmail($email))
        {
            return ['code'=>0,'message'=>'邮件地址不正确'];
        }

        $user_info = Users::checkUserExist($email);
        $from_username = $data['form_username'] ?? '';
        $link = $data['link'] ?? '';
        $link_title = $data['link_title'] ?? '';
        $parseData = self::parseTemplate($email_type,$user_info['user_name'],$from_username,$link,$link_title);
        $subject = $data['subject'] ?? $parseData['subject'];
        $message = $data['message'] ?? $parseData['message'];
        $email = is_array($email) ? $email : explode(',',$email);
        try {
            $mail->CharSet="utf-8";
            $mail->Encoding = "base64";
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
            $mail->isSMTP();                                         //Send using SMTP
            $mail->Host       = $emailConfig['email_host'];                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = $emailConfig['email_username'];                     //SMTP username
            $mail->Password   = $emailConfig['email_password'];                               //SMTP password
            $mail->SMTPSecure = $emailConfig['email_secure'] ? : PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = $emailConfig['email_port'] ? : 25;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            $mail->From        =  $emailConfig['email_from'];
            $mail->FromName    =  $emailConfig['email_show_name'];
            //添加附件
            if(isset($data['attachments']) && $data['attachments'])
            {
                $attachments = is_array($data['attachments']) ? $data['attachments'] : explode(',',$data['attachments']);
                foreach ($attachments as $key=>$val)
                {
                    $mail->addAttachment($val);
                }
            }

            //设置内容
            foreach ($email as $key=>$val)
            {
                $mail->addAddress($val);
            }
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->send();
            $emailLogData = [
                'send_to'=>implode(',',$email),
                'subject'=>$subject,
                'message'=>$message,
                'status'=>1,
                'error_message'=>'',
                'create_time'=>time()
            ];
            db('email_log')->insert($emailLogData);
            return ['code'=>1,'message'=>'邮件发送成功'];
        } catch (Exception $e) {
            $emailLogData = [
                'send_to'=>implode(',',$email),
                'subject'=>$subject,
                'message'=>$message,
                'status'=>0,
                'error_message'=>$mail->ErrorInfo,
                'create_time'=>time()
            ];
            db('email_log')->insert($emailLogData);
            return ['code'=>0,'message'=>'邮件发送失败,错误信息：'.$mail->ErrorInfo];
        }
    }

    /**
     * 解析邮件模板
     * @param $type
     * @param $user_name
     * @param $form_username
     * @param null $link
     * @param null $link_title
     * @return array
     */
    public static function parseTemplate($type,$user_name,$form_username,$link = null, $link_title = null): array
    {
        $typeInfo = config('email.'.$type);
        $subject = self::replaceEmailTemplate($typeInfo['subject'],$user_name,$form_username,'','',$link,$link_title);
        $message = self::replaceEmailTemplate($typeInfo['message'],$user_name,$form_username,'','',$link,$link_title);
        $template = self::replaceEmailTemplate(self::getTemplate(root_path().'app/common/tpl/email.tpl'),$user_name,$form_username,$subject,$message,$link,$link_title);
        return ['subject'=>$subject,'message'=>$template];
    }

    /**
     * 替换模板内容
     * @param $template
     * @param $user_name
     * @param $form_username
     * @param $subject
     * @param $message
     * @param null $link
     * @param null $link_title
     * @return array|string|string[]
     */
    public static function replaceEmailTemplate($template,$user_name,$form_username, $subject, $message, $link = null, $link_title = null)
    {
        return str_replace(['[#site_name#]','[#subject#]','[#message#]','[#time#]','[#user_name#]','[#from_username#]','[#link#]','[#link_title#]'],[get_setting('site_name'),$subject,$message,formatTime(time()),$user_name,$form_username,$link,$link_title],$template);
    }
}