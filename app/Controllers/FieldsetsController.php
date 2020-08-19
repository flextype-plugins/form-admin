<?php

declare(strict_types=1);

namespace Flextype\Plugin\FormAdmin\Controllers;

use Flextype\Component\Arrays\Arrays;
use function date;
use function Flextype\Component\I18n\__;

class FieldsetsController
{
    /**
     * Flextype Application
     */
     protected $flextype;

    /**
     * __construct
     */
     public function __construct($flextype)
     {
         $this->flextype = $flextype;
     }

    public function index($request, $response)
    {
        return $this->flextype->container('twig')->render(
            $response,
            'plugins/form-admin/templates/extends/fieldsets/index.html',
            [
                'menu_item' => 'fieldsets',
                'fieldsets_list' => $this->flextype->container('fieldsets')->fetchAll(),
                'links' =>  [
                    'fieldsets' => [
                        'link' => $this->flextype->container('router')->pathFor('admin.fieldsets.index'),
                        'title' => __('form_admin_fieldsets'),
                        'active' => true
                    ],
                ],
                'buttons' => [
                    'fieldsets_add' => [
                        'link' => $this->flextype->container('router')->pathFor('admin.fieldsets.add'),
                        'title' => __('form_admin_create_new_fieldset')
                    ]
                ],
            ]
        );
    }

    public function add($request, $response)
    {
        return $this->flextype->container('twig')->render(
            $response,
            'plugins/form-admin/templates/extends/fieldsets/add.html',
            [
                'menu_item' => 'fieldsets',
                'fieldsets_list' => $this->flextype->container('fieldsets')->fetchAll(),
                'links' =>  [
                    'fieldsets' => [
                        'link' => $this->flextype->container('router')->pathFor('admin.fieldsets.index'),
                        'title' => __('form_admin_fieldsets'),
                    ],
                    'fieldsets_add' => [
                        'link' => $this->flextype->container('router')->pathFor('admin.fieldsets.add'),
                        'title' => __('form_admin_create_new_fieldset'),
                        'active' => true
                    ],
                ],
            ]
        );
    }

    public function addProcess($request, $response)
    {
        // Get data from POST
        $post_data = $request->getParsedBody();

        Arrays::delete($post_data, 'csrf_name');
        Arrays::delete($post_data, 'csrf_value');

        $id   = $this->flextype->container('slugify')->slugify($post_data['id']);
        $data = [
            'title' => $post_data['title'],
            'default_field' => 'title',
            'icon' => $post_data['icon'],
            'hide' => (bool) $post_data['hide'],
            'form' => [
                'tabs' => [
                    'main' => [
                        'title' => 'admin_main',
                        'form' => [
                            'fields' => [
                                'title' => [
                                    'title' => 'admin_title',
                                    'type' => 'text',
                                    'size' => '12',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        if ($this->flextype->container('fieldsets')->create($id, $data)) {
            $this->flextype->container('flash')->addMessage('success', __('form_admin_message_fieldset_created'));
        } else {
            $this->flextype->container('flash')->addMessage('error', __('form_admin_message_fieldset_was_not_created'));
        }

        if (isset($post_data['create-and-edit'])) {
            return $response->withRedirect($this->flextype->container('router')->pathFor('admin.fieldsets.edit') . '?id=' . $id);
        }

        return $response->withRedirect($this->flextype->container('router')->pathFor('admin.fieldsets.index'));
    }

    public function edit($request, $response)
    {
        return $this->flextype->container('twig')->render(
            $response,
            'plugins/form-admin/templates/extends/fieldsets/edit.html',
            [
                'menu_item' => 'fieldsets',
                'id' => $request->getQueryParams()['id'],
                'data' => $this->flextype->container('yaml')->encode($this->flextype->container('fieldsets')->fetch($request->getQueryParams()['id'])),
                'links' =>  [
                    'fieldsets' => [
                        'link' => $this->flextype->container('router')->pathFor('admin.fieldsets.index'),
                        'title' => __('form_admin_fieldsets'),
                    ],
                    'fieldsets_editor' => [
                        'link' => $this->flextype->container('router')->pathFor('admin.fieldsets.edit') . '?id=' . $request->getQueryParams()['id'],
                        'title' => __('form_admin_editor'),
                        'active' => true
                    ],
                ],
                'buttons' => [
                    'save_entry' => [
                        'type' => 'action',
                        'link' => 'javascript:;',
                        'title' => __('form_admin_save')
                    ],
                ],
            ]
        );
    }

    public function editProcess($request, $response)
    {
        $id   = $request->getParsedBody()['id'];
        $data = $request->getParsedBody()['data'];

        if ($this->flextype->container('fieldsets')->update($request->getParsedBody()['id'], $this->flextype->container('yaml')->decode($data))) {
            $this->flextype->container('flash')->addMessage('success', __('form_admin_message_fieldset_saved'));
        } else {
            $this->flextype->container('flash')->addMessage('error', __('form_admin_message_fieldset_was_not_saved'));
        }

        return $response->withRedirect($this->flextype->container('router')->pathFor('admin.fieldsets.edit') . '?id=' . $id);
    }

    public function rename($request, $response)
    {
        return $this->flextype->container('twig')->render(
            $response,
            'plugins/form-admin/templates/extends/fieldsets/rename.html',
            [
                'menu_item' => 'fieldsets',
                'id' => $request->getQueryParams()['id'],
                'links' =>  [
                    'fieldsets' => [
                        'link' => $this->flextype->container('router')->pathFor('admin.fieldsets.index'),
                        'title' => __('form_admin_fieldsets'),
                    ],
                    'fieldsets_rename' => [
                        'link' => $this->flextype->container('router')->pathFor('admin.fieldsets.rename') . '?id=' . $request->getQueryParams()['id'],
                        'title' => __('form_admin_rename'),
                        'active' => true
                    ],
                ],
            ]
        );
    }

    public function renameProcess($request, $response)
    {
        if ($this->flextype->container('fieldsets')->rename($request->getParsedBody()['fieldset-id-current'], $request->getParsedBody()['id'])) {
            $this->flextype->container('flash')->addMessage('success', __('form_admin_message_fieldset_renamed'));
        } else {
            $this->flextype->container('flash')->addMessage('error', __('form_admin_message_fieldset_was_not_renamed'));
        }

        return $response->withRedirect($this->flextype->container('router')->pathFor('admin.fieldsets.index'));
    }

    public function deleteProcess($request, $response)
    {
        if ($this->flextype->container('fieldsets')->delete($request->getParsedBody()['fieldset-id'])) {
            $this->flextype->container('flash')->addMessage('success', __('form_admin_message_fieldset_deleted'));
        } else {
            $this->flextype->container('flash')->addMessage('error', __('form_admin_message_fieldset_was_not_deleted'));
        }

        return $response->withRedirect($this->flextype->container('router')->pathFor('admin.fieldsets.index'));
    }

    public function duplicateProcess($request, $response)
    {
        if ($this->flextype->container('fieldsets')->copy($request->getParsedBody()['fieldset-id'], $request->getParsedBody()['fieldset-id'] . '-duplicate-' . date('Ymd_His'))) {
            $this->flextype->container('flash')->addMessage('success', __('form_admin_message_fieldset_duplicated'));
        } else {
            $this->flextype->container('flash')->addMessage('error', __('form_admin_message_fieldset_was_not_duplicated'));
        }

        return $response->withRedirect($this->flextype->container('router')->pathFor('admin.fieldsets.index'));
    }
}
