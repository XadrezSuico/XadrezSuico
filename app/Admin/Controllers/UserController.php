<?php

namespace App\Admin\Controllers;

use App\User;
use App\Perfil;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Listar Usuários')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Mostrar Usuário')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Editar Usuário')
            ->description('description')
            ->body($this->form_edit()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Criar Usuário')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);

        $grid->id('#');
        $grid->name('Nome');
        $grid->email('Email');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->id('ID');
        $show->name('Nome');
        $show->email('Email');
        $show->created_at('Criado em');
        $show->updated_at('Atualizado em');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);

        $form->tab('Informações Básicas', function ($form) {

            $form->text('name', 'Nome');
            $form->email('email', 'Email');
            $form->password('password', 'Senha');

        })->tab('Perfis', function ($form) {

            $form->hasMany('perfis', function ($form) {
                $form->select('perfils_id', 'Perfil')->options(Perfil::all()->pluck('name', 'id'));
            });

        });
        $form->submitted(function (Form $form) {
            //...
        });

        return $form;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form_edit()
    {
        $form = new Form(new User);

        $form->tab('Informações Básicas', function ($form) {

            $form->text('name', 'Nome');
            $form->email('email', 'Email');

        })->tab('Perfis', function ($form) {

            $form->hasMany('perfis', function ($form) {
                $form->select('perfils_id', 'Perfil')->options(Perfil::all()->pluck('name', 'id'));
            });

        });
        $form->submitted(function (Form $form) {
            //...
        });

        return $form;
    }
}
