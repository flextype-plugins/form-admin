<?php

declare(strict_types=1);

use Flextype\Plugin\Acl\Middlewares\AclIsUserLoggedInMiddleware;
use Flextype\Plugin\Acl\Middlewares\AclIsUserLoggedInRolesInMiddleware;

flextype()->group('/' . $admin_route, function () {
    flextype()->get('/fieldsets', 'FieldsetsController:index')->setName('admin.fieldsets.index');
    flextype()->get('/fieldsets/add', 'FieldsetsController:add')->setName('admin.fieldsets.add');
    flextype()->post('/fieldsets/add', 'FieldsetsController:addProcess')->setName('admin.fieldsets.addProcess');
    flextype()->get('/fieldsets/edit', 'FieldsetsController:edit')->setName('admin.fieldsets.edit');
    flextype()->post('/fieldsets/edit', 'FieldsetsController:editProcess')->setName('admin.fieldsets.editProcess');
    flextype()->get('/fieldsets/rename', 'FieldsetsController:rename')->setName('admin.fieldsets.rename');
    flextype()->post('/fieldsets/rename', 'FieldsetsController:renameProcess')->setName('admin.fieldsets.renameProcess');
    flextype()->post('/fieldsets/duplicate', 'FieldsetsController:duplicateProcess')->setName('admin.fieldsets.duplicateProcess');
    flextype()->post('/fieldsets/delete', 'FieldsetsController:deleteProcess')->setName('admin.fieldsets.deleteProcess');

})->add(new AclIsUserLoggedInMiddleware(['redirect' => 'admin.accounts.login']))
  ->add(new AclIsUserLoggedInRolesInMiddleware(['redirect' => (flextype()->getContainer()->acl->isUserLoggedIn() ? 'admin.accounts.no-access' : 'admin.accounts.login'),
                                                     'roles' => 'admin']))
  ->add('csrf');
