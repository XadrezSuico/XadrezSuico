<?php

namespace App\Admin\Controllers;

use App\Torneio;
use App\TipoTorneio;
use App\Evento;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class TorneioController extends Controller
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
            ->header('Listar Torneios do Evento')
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
            ->header('Mostrar Torneio do Evento')
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
            ->header('Editar Torneio do Evento')
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
            ->header('Criar Torneio do Evento')
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
        $grid = new Grid(new Torneio);
        $grid->id('#');
        $grid->name('Nome do Torneio');
        $grid->evento_id('Evento')->display(function($evento_id) {
            return Evento::find($evento_id)->name;
        });
        $grid->tipo_torneio_id('Tipo de Torneio')->display(function($tipo_torneio_id) {
            return TipoTorneio::find($tipo_torneio_id)->name;
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
        $show = new Show(Torneio::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Torneio);
        $form->text('name', 'Nome do Evento');
        $form->select('evento_id', 'Evento')->options(Evento::all()->pluck('name', 'id'));
        $form->select('tipo_torneio_id', 'Tipo de Torneio')->options(TipoTorneio::all()->pluck('name', 'id'));
        return $form;
    }
}
