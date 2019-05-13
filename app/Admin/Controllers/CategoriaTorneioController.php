<?php

namespace App\Admin\Controllers;

use App\CategoriaTorneio;
use App\Categoria;
use App\Torneio;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CategoriaTorneioController extends Controller
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
            ->header('Listar Categorias dos Torneios')
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
            ->header('Mostrar Categoria do Torneio')
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
            ->header('Editar Categoria do Torneio')
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
            ->header('Vincular Categoria ao Torneio')
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
        $grid = new Grid(new CategoriaTorneio);
        $grid->id('#');
        $grid->torneio_id('Torneio')->display(function($torneio_id) {
            return Torneio::find($torneio_id)->name;
        });
        $grid->categoria_id('Categoria')->display(function($categoria_id) {
            return Categoria::find($categoria_id)->name;
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
        $show = new Show(CategoriaTorneio::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CategoriaTorneio);
        $form->select('torneio_id', 'Torneio')->options(function() {
            // return Categoria::find($categoria_id)->name;
            $return = array();
            foreach(Torneio::all() as $torneio){
                $return[$torneio->id] = $torneio->evento->name." - ".$torneio->name;
            }
            return $return;
        });
        $form->select('categoria_id', 'Categoria')->options(Categoria::all()->pluck('name', 'id'));
        return $form;
    }
}
