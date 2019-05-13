<?php

namespace App\Admin\Controllers;

use App\UserPerfil;
use App\User;
use App\Perfil;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserPerfilController extends Controller
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
            ->header('Index')
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
            ->header('Detail')
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
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
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
            ->header('Create')
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
        $grid = new Grid(new UserPerfil);

        $grid->id('#');
        $grid->users_id("Usuário")->display(function($id) {
            return User::find($id)->name;
        });
        $grid->perfils_id('Perfil')->display(function($id) {
            return Perfil::find($id)->name;
        });

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
        $show = new Show(UserPerfil::findOrFail($id));

        $show->id('#');
        $show->users_id('Usuário')->display(function($id) {
            return User::find($id)->name;
        });
        $show->perfils_id('Perfil')->display(function($id) {
            return Perfil::find($id)->name;
        });
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
        $form = new Form(new UserPerfil);

        $form->select('users_id', 'Usuário')->options(User::all()->pluck('name', 'id'));
        $form->select('perfils_id', 'Perfil')->options(Perfil::all()->pluck('name', 'id'));

        return $form;
    }
}
