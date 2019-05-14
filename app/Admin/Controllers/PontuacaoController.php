<?php

namespace App\Admin\Controllers;

use App\Pontuacao;
use App\Torneio;
use App\Evento;
use App\GrupoEvento;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class PontuacaoController extends Controller
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
            ->header('Listar Pontuações')
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
            ->header('Mostrar Pontuação')
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
            ->header('Editar Pontuação')
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
            ->header('Criar Pontuação')
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
        $grid = new Grid(new Pontuacao);
        $grid->id('#');
        $grid->posicao('Posição');
        $grid->pontuacao('Pontuação');
        $grid->torneio_id('Torneio')->display(function($torneio_id) {
            if(Torneio::find($torneio_id)){
                return Torneio::find($torneio_id)->name;
            }
        });
        $grid->evento_id('Evento')->display(function($evento_id) {
            if(Evento::find($evento_id)){
                return Evento::find($evento_id)->name;
            }
        });
        $grid->grupo_evento_id('Grupo de Evento')->display(function($grupo_evento_id) {
            if(GrupoEvento::find($grupo_evento_id)){
                return GrupoEvento::find($grupo_evento_id)->name;
            }
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
        $show = new Show(Pontuacao::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Pontuacao);
        $form->number('posicao', 'Posição');
        $form->number('pontuacao', 'Pontuação');
        $form->select('torneio_id', 'Torneio')->options(Torneio::all()->pluck('name', 'id'));
        $form->select('evento_id', 'Evento')->options(Evento::all()->pluck('name', 'id'));
        $form->select('grupo_evento_id', 'Grupo de Evento')->options(GrupoEvento::all()->pluck('name', 'id'));
        return $form;
    }
}
