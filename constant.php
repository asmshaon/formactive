<?php
define("WEBSITE_NAME","form@ctivate");
define("ADMIN_WEBSITE_NAME","form@ctivate");

define("WEBSITE_URL","http://www.formactivate.com/dev/");
define("WEBROOT_PATH","/kunden/homepages/6/d345917233/htdocs/dev/");
define("__VIRTUAL_DIRECTORY__","/dev/");

define("ADMIN_EMAIL","steve@highclarity.com");
define("SITE_SUPPORT_EMAIL","steve@highclarity.com");
define("WEBSITE_JS_URL",WEBSITE_URL."library/JS/");
define("WEBSITE_CSS_URL",WEBSITE_URL."css/");
define("WEBSITE_EXTERNALJS_URL",WEBSITE_URL."js/");
define("WEBSITE_IMG_URL",WEBSITE_URL."images/");
define("__JS_DIRECTORY__",__VIRTUAL_DIRECTORY__."library/JS/");
define("TODAY_DATE_TIME",date("Y-m-d H:i:s"));
define("TWILIO_API_VERSION","2010-04-01");
define("TWILIO_ACCOUNT_SID","AC2dbe8a176e89ff7f6641d4d03c047bca");
define("TWILIO_AUTH_TOKEN","1a0e256d656ebef49cbe84cd5e441501");
define("TWILIO_STATUS_CALL_BACK_URL","http://www.formactivate.com/dev/status_call_back_url.php");
define("ENVIRONMENT","sandbox");

//define("MERCHANTID","jf5qp9jc6wsb2gs7");
//define("PUBLICKEY","9pdnyn263m8p3hg5");
//define("PRIVATEKEY","7dysmj56j7fs88bf");
// client sand box upper

define("MERCHANTID","ymtdbq99n9wgk54z");
define("PUBLICKEY","rv2krj3t92pbq5m9");
define("PRIVATEKEY","4gys57d496bj55yp");


//////////////////////////////////////


define("SIGNUP_FIRST_NAME","Please enter your first name");
define("SIGNUP_LAST_NAME","Please enter your last name");
define("SIGNUP_EMAIL","Please enter your email addressfor the varification of your account");
define("SIGNUP_PASS1","Please specify a password that contains a combination of letters and numbers");
define("SIGNUP_PASS2","Please type above password again");

define("SIGNUP_CREDIT_CARD_TYPE","Select your credit card type");
define("SIGNUP_CREDIT_CARD_NUMBER","Please type your credit card number carefully");
define("SIGNUP_CREDIT_CARD_EXPIRE","Select your credit card expriation year and month");
define("SIGNUP_CREDIT_CARD_NAME_ON_CARD","Type your credit card name");
define("SIGNUP_CVV","For Visa 3 digits, for American express 4 digits");

define("SIGNUP_ADDRESS1","Please type your billing address1");
define("SIGNUP_CITY","Please type your city");
define("SIGNUP_STATE","Please type your state");
define("SIGNUP_ZIP","Please type your zip code");
define("SIGNUP_PHONE","Please type your billing phone number, where we can call you");


/* END FOR SIGNUP FORM */


/* START FOR FORMS CREATION */

define("OVERVIEW_FORM_NAME","A meaningfull form name, like contact us, order now");
define("OVERVIEW_FORM_BUSNESPH","Provide a valid business phone number, so that you can get necessary calls when your form is submitted");
define("OVERVIEW_FORM_HOMEPH","Provide a valid home phone number,so that you can get necessary calls when your form is submitted ");
define("OVERVIEW_FORM_CALLER_ID","Select at least caller id, from drop down");

define("STDFIELD_PREVIEW","Preview your form when you complete your all step for form creationn currectly.");

define("STDFIELD_HELP","You can choose any field, validattion, for your form so that your user can get this field when you embeded into your website.");

define("CUSTOME_FORM_HELP","You can choose any field, validattion, for your form so that your user get this field when you embeded into your website.");

define("BUSINES_HOUR_HELP","You can choose your business time, so that only at that time you can get phone call, mail..");

define("BISNESRULE_RULE_NAME","Please type a rule name, like day rule");
define("BISNESRULE_STATUS","This rule is active or not ?");
define("BISNESRULE_CONDITION","Select rule condition carefully");
define("BISNESRULE_TIME","Specify the time of business rule");
define("BISNESRULE_CON_NUMBER","Define connection number");
define("BISNESRULE_CUSTOM_FIELD","Select from your custom fields");
define("BISNESRULE_CUSTOM_FIELD_VALUE","Type selected custom field value here");

define("INCOMPLETE_CALL","You will be notified case of incomplete call");
define("AFTER_HOUR_CALL","You will be notified case of after hour call call");
define("UNANSWER_CALL","You will be notified case of unanswer call");
define("ANSWER_CALL","You will be notified case of answers call");
define("CONNECTIONS","You will be notified case of connections call");
define("PROS_EMAIL","You will be notified by email or not");

define("REDIRECT_TYPE","Redirect type means where redirect your form action after submiting user data.");
define("REDIRECT_URL","This URL will be used after form submission");

define("OLD_PASSWORD","Please type your old password carefully");

?>