Cake 4 Document

   *** Setup Database in Local (see config in app/config/bootstrap.php line 81 - 94) 

- App/app_local.php 

    'Datasource' => [
        'default' => [
             …. Config here ….
        ]
    ]


   *** Import file default sql into php my admin

- App/config/schema/default.sql


 *** Setup for email (from, to , subject, template, emailFormat)

- App/config/app.php

    'Email' => [
        'item_email_1' => [
            …. Config here ….
        ],
        'item_email_1' => [
            …. Config here ….
        ],
        ….
    ]


   *** Use object mail

	$email = new Mailer('item_email_1'); // “item_email_1” configured above ”Email”
 	$email->setViewVars(['_' => $data]);  // “$data” data transfer to format file of email (*** NOTE / “format for email”)           
    $r = $email->deliver();
    unset($email);

     * Can change config of email in code by

        $email->{function here}()

        Example: 
            $email->setTo(‘to other email’);
		    $email->setSubject(‘New Subject’);
		    $email->viewBuilder()->setTemplate(‘new template’);
		….


   ** NOTE

- Load helper for view in app/src/View/AppView.php in function initialize()
- All file “Model” saved in app/src/Model/Table
- All file “view Element” saved in app/templates/element/
- All file “Layout” saved in app/templates/layout/
- All file “format for email” saved in app/templates/email/
- All file “css, js, images … of [object]’ saved public_html/[object]/[css || js || images…]/

/** Admin */
- All file “View of Admin” saved in app/templates/Admin/
- All file “Controller of Admin” saved in app/src/Controller/Admin/
- All file “css, js, images … of [object] in Admin’ saved public_html/admin/[object]/[css || js || images…]/