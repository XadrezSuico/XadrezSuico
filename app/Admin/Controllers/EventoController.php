<?php

namespace App\Admin\Controllers;

use App\Evento;
use App\GrupoEvento;
use App\Cidade;
use App\Torneio;
use App\Categoria;
use App\CategoriaEvento;
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
        
        $form->tab('Informações Básicas', function ($form) {
            $form->text('name', 'Nome do Evento');
            $form->date('data_inicio', 'Data de Início');
            $form->date('data_fim', 'Data de Fim');
            $form->text('local', 'Local do Evento');
            $form->text('link', 'Link do Evento');
            $form->select('cidade_id', 'Cidade')->options(Cidade::all()->pluck('name', 'id'));
            $form->select('grupo_evento_id', 'Grupo de Evento')->options(GrupoEvento::all()->pluck('name', 'id'));
        });
        
        $form->saved(function (Form $form) {

            foreach($form->model()->grupo_evento->torneios->all() as $torneio_template){
                $torneio = new Torneio;
                $torneio->name = $torneio_template->name; 
                $torneio->evento_id = $form->model()->id; 
                $torneio->tipo_torneio_id = 1; 
                $torneio->torneio_template_id = $torneio_template->id; 
                $torneio->save();

                foreach($torneio_template->categorias->all() as $categoria){
                    $categoria_torneio = new CategoriaTorneio;
                    $categoria_torneio->categoria_id = $categoria->categoria->id; 
                    $categoria_torneio->torneio_id = $torneio->id; 
                    $categoria_torneio->save();
                }
            }

            foreach($form->model()->grupo_evento->categorias->all() as $categoria){
                $categoria_evento = new CategoriaEvento;
                $categoria_evento->categoria_id = $categoria->categoria->id; 
                $categoria_evento->evento_id = $evento->id; 
                $categoria_evento->save();
            }

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
        $form = new Form(new Evento);

        $form->tab('Informações Básicas', function ($form) {
            $form->text('name', 'Nome do Evento');
            $form->date('data_inicio', 'Data de Início');
            $form->date('data_fim', 'Data de Fim');
            $form->text('local', 'Local do Evento');
            $form->text('link', 'Link do Evento');
            $form->select('cidade_id', 'Cidade')->options(Cidade::all()->pluck('name', 'id'));
            $form->select('grupo_evento_id', 'Grupo de Evento')->options(GrupoEvento::all()->pluck('name', 'id'));
        })->tab('Categorias', function ($form) {
            $form->hasMany('categorias', function ($form) {
                $form->select('categoria_id', 'Categoria')->options(Categoria::all()->pluck('name', 'id'));
            });
        })->tab('Torneios', function ($form) {
            $form->hasMany('torneios', function ($form) {
                $form->select('torneio_id', 'Torneio')->options(Categoria::all()->pluck('name', 'id'));
            });
        });


        return $form;
    }
}
