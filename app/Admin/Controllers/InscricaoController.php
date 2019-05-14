<?php

namespace App\Admin\Controllers;

use App\Inscricao;
use App\Enxadrista;
use App\Categoria;
use App\Torneio;
use App\Cidade;
use App\Clube;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Illuminate\Support\MessageBag;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class InscricaoController extends Controller
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
            ->header('Listar Inscrições dos Torneios')
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
            ->header('Mostrar Inscrição do Torneio')
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
            ->header('Editar Inscrição no Torneio')
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
            ->header('Criar Inscrição no Torneio')
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
        $grid = new Grid(new Inscricao);
        $grid->id('#');
        $grid->enxadrista_id('Enxadrista')->display(function($enxadrista_id) {
            return Enxadrista::find($enxadrista_id)->name;
        });
        $grid->torneio_id('Torneio')->display(function($torneio_id) {
            return Torneio::find($torneio_id)->name;
        });
        $grid->categoria_id('Categoria')->display(function($categoria_id) {
            return Categoria::find($categoria_id)->name;
        });
        $grid->cidade_id('Cidade')->display(function($cidade_id) {
            return Cidade::find($cidade_id)->name;
        });
        $grid->clube_id('Clube')->display(function($clube_id) {
            if($clube_id) return Clube::find($clube_id)->name;
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
        $show = new Show(Inscricao::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Inscricao);
        $form->select('enxadrista_id', 'Enxadrista')->options(Enxadrista::all()->pluck('name', 'id'))->rules(function($form){

        });
        $form->select('torneio_id', 'Torneio')->options(Torneio::all()->pluck('name', 'id'));
        $form->select('categoria_id', 'Categoria')->options(Categoria::all()->pluck('name', 'id'));
        $form->select('cidade_id', 'Cidade')->options(Cidade::all()->pluck('name', 'id'));
        $form->select('clube_id', 'Clube')->options(Clube::all()->pluck('name', 'id'));
        $form->checkbox('confirmado', 'Confirmado?')->options(['confirmado' => 'Confirmado?']);;

        $form->saving(function (Form $form) {            
            $form->model()->regulamento_aceito = true;
            $inscricao = Inscricao::whereHas("torneio",function($q1) use ($form){
                $q1->where([["id","=",$form->torneio_id]]);
                $q1->whereHas("evento",function($q2) use ($form){
                    $q2->whereHas("torneios",function($q3) use ($form){
                        $q3->whereHas("inscricoes",function($q4) use ($form) {
                            if($form->model()->id){
                                $q4->where([["enxadrista_id","=",$form->enxadrista_id],["id","!=",$form->model()->id]]);
                            }else{
                                $q4->where([["enxadrista_id","=",$form->enxadrista_id]]);
                            }
                        });
                    });
                });
                $q1->where([["enxadrista_id","=",$form->enxadrista_id]]);
            });
            // $sql = $inscricao->toSql();
            $count = $inscricao->count();
            $inscricao = $inscricao->first();
            if($count > 0){
                 $error = new MessageBag([
                    'title'   => 'ERRO!',
                    'message' => 'O enxadrista já se encontra cadastrado neste evento! Inscrição #'.$inscricao->id,
                ]);

                return back()->with(compact('error'));
            }


            if(isset($form->confirmado["confirmado"])){
                $form->confirmado = 1; 
            }else{
                $form->confirmado = 0;
            }
        });

        return $form;
    }
}
