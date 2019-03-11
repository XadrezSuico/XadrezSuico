<?php

namespace App\Admin\Controllers;

use App\CategoriaTorneioTemplate;
use App\TorneioTemplate;
use App\Categoria;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CategoriaTorneioTemplateController extends Controller
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
            ->header('Listar Categorias do Template de Torneio')
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
            ->header('Mostrar Categoria do Template de Torneio')
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
            ->header('Editar Categoria do Template de Torneio')
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
            ->header('Relacionar Categoria ao Template de Torneio')
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
        $grid = new Grid(new CategoriaTorneioTemplate);
        $grid->id('#');
        $grid->torneio_template_id('Template de Torneio')->display(function($torneio_template_id) {
            return TorneioTemplate::find($torneio_template_id)->name;
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
        $show = new Show(CategoriaTorneioTemplate::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CategoriaTorneioTemplate);
        $form->select('torneio_template_id', 'Template de Torneio')->options(TorneioTemplate::all()->pluck('name', 'id'));
        $form->select('categoria_id', 'Categoria')->options(Categoria::all()->pluck('name', 'id'));
        return $form;
    }
}
