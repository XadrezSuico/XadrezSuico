<?php

namespace App\Admin\Controllers;

use App\Evento;
use App\GrupoEvento;
use App\Cidade;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class EventoController extends Controller
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
            ->header('Listar Eventos')
            ->description('')
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
            ->header('Mostrar Evento')
            ->description('')
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
            ->header('Editar Evento')
            ->description('')
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
            ->header('Criar Evento')
            ->description('')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Evento);
        $grid->id('#');
        $grid->name('Nome do Evento');
        $grid->data_inicio('Data de Início');
        $grid->data_fim('Data de Fim');
        $grid->local('Local');
        $grid->grupo_evento_id('Grupo de Evento')->display(function($grupo_evento_id) {
            return GrupoEvento::find($grupo_evento_id)->name;
        });
        $grid->cidade_id('Cidade')->display(function($cidade_id) {
            return Cidade::find($cidade_id)->name;
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
        $show = new Show(Evento::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Evento);
        $form->text('name', 'Nome do Evento');
        $form->date('data_inicio', 'Data de Início');
        $form->date('data_fim', 'Data de Fim');
        $form->text('local', 'Local do Evento');
        $form->select('cidade_id', 'Cidade')->options(Cidade::all()->pluck('name', 'id'));
        $form->select('grupo_evento_id', 'Grupo de Evento')->options(GrupoEvento::all()->pluck('name', 'id'));



        return $form;
    }
}
