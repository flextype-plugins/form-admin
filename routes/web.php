<?php

declare(strict_types=1);

namespace Flextype;

$app->group('/' . $admin_route, function () use ($app, $flextype) : void {

    // FieldsetsController
    $app->get('/fieldsets', 'FieldsetsController:index')->setName('admin.fieldsets.index');
    $app->get('/fieldsets/add', 'FieldsetsController:add')->setName('admin.fieldsets.add');
    $app->post('/fieldsets/add', 'FieldsetsController:addProcess')->setName('admin.fieldsets.addProcess');
    $app->get('/fieldsets/edit', 'FieldsetsController:edit')->setName('admin.fieldsets.edit');
    $app->post('/fieldsets/edit', 'FieldsetsController:editProcess')->setName('admin.fieldsets.editProcess');
    $app->get('/fieldsets/rename', 'FieldsetsController:rename')->setName('admin.fieldsets.rename');
    $app->post('/fieldsets/rename', 'FieldsetsController:renameProcess')->setName('admin.fieldsets.renameProcess');
    $app->post('/fieldsets/duplicate', 'FieldsetsController:duplicateProcess')->setName('admin.fieldsets.duplicateProcess');
    $app->post('/fieldsets/delete', 'FieldsetsController:deleteProcess')->setName('admin.fieldsets.deleteProcess');

})->add(new AclAccountIsUserLoggedInMiddleware(['container' => $flextype, 'redirect' => 'admin.accounts.login']))
  ->add(new AclAccountsIsUserLoggedInRolesOneOfMiddleware(['container' => $flextype,
                                                           'redirect' => ($flextype->acl->isUserLoggedIn() ? 'admin.accounts.no-access' : 'admin.accounts.login'),
                                                           'roles' => 'admin']))
  ->add('csrf');
