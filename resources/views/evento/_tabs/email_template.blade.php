                <br/>
                <section class="col-lg-12 connectedSortable">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">Templates de E-mail</h3>
                        </div>
                        <!-- form start -->
                            <div class="box-body">
                                <table id="tabela_email_templates" class="table-responsive table-condensed table-striped" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nome</th>
                                            <th>Assunto do E-mail</th>
                                            <th width="20%">Opções</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($evento->email_templates->all() as $template)
                                            <tr>
                                                <td>{{$template->id}}</td>
                                                <td>{{$template->name}}</td>
                                                <td>{{$template->subject}}</td>
                                                <td>
                                                    <a class="btn btn-default" href="{{url("/emailtemplate/edit/".$template->id)}}" role="button">Editar</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                    </div>
                </section>
            
