<br />
<section class="col-lg-12 connectedSortable">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Resumo</h3>
        </div>
        <!-- form start -->

        <div class="box-body">

            <div class="row">
                <!-- Total de Inscritos -->
                <div class="col-sm-12 col-md-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{ $evento->quantosInscritos() }}</h3>

                            <p>Total de Inscritos</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-users"></i>
                        </div>
                        <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                    </div>
                </div>
                <!-- Total de Confirmados -->
                <div class="col-sm-12 col-md-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{ $evento->quantosInscritosConfirmados() }}</h3>

                            <p>Total de Confirmados</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-check"></i>
                        </div>
                        <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                    </div>
                </div>
                <!-- Total de Presentes -->
                <div class="col-sm-12 col-md-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{ $evento->quantosInscritosPresentes() }}</h3>

                            <p>Total de Presentes</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                    </div>
                </div>
                <!-- Total de Resultados (Importados) -->
                <div class="col-sm-12 col-md-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{ $evento->quantosInscritosComResultados() }}</h3>

                            <p>Total de Resultados</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                    </div>
                </div>
            </div>
            <div class="row">
                @if ($evento->isPaid())
                    <!-- Total de Pagos -->
                    <div class="col-sm-6 col-md-4">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $evento->howManyPaid() }}</h3>

                                <p>Total de Pagos</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                        </div>
                    </div>
                    <!-- Total de Gratuidades -->
                    <div class="col-sm-6 col-md-4">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $evento->howManyFree() }}</h3>

                                <p>Total de Gratuidades (Categorias Gratuitas)</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                        </div>
                    </div>
                    <!-- Total de Não Pagos -->
                    <div class="col-sm-6 col-md-4">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $evento->howManyNotPaid() }}</h3>

                                <p>Total de Não Pagos</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                        </div>
                    </div>


                    <!-- Total de Confirmados Pagos -->
                    <div class="col-sm-6 col-md-4">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $evento->howManyConfirmedPaid() }}</h3>

                                <p>Total de Confirmados Pagos</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                        </div>
                    </div>
                    <!-- Total de Confirmados Gratuidades -->
                    <div class="col-sm-6 col-md-4">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $evento->howManyConfirmedFree() }}</h3>

                                <p>Total de Confirmados Gratuidades</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                        </div>
                    </div>
                    <!-- Total de Confirmados Não Pagos -->
                    <div class="col-sm-6 col-md-4">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $evento->howManyConfirmedNotPaid() }}</h3>

                                <p>Total de Confirmados Não Pagos</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                        </div>
                    </div>


                    <!-- Total de Presentes Pagos -->
                    <div class="col-sm-6 col-md-4">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $evento->howManyPresentPaid() }}</h3>

                                <p>Total de Presentes Pagos</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                        </div>
                    </div>
                    <!-- Total de Presentes Gratuidades -->
                    <div class="col-sm-6 col-md-4">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $evento->howManyPresentFree() }}</h3>

                                <p>Total de Presentes Gratuidades</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                        </div>
                    </div>
                    <!-- Total de Presentes Não Pagos -->
                    <div class="col-sm-6 col-md-4">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $evento->howManyPresentNotPaid() }}</h3>

                                <p>Total de Presentes Não Pagos</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                        </div>
                    </div>

                    <!-- Total de Resultados Pagos -->
                    <div class="col-sm-6 col-md-4">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $evento->howManyWithResultsPaid() }}</h3>

                                <p>Total de Resultados Pagos</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                        </div>
                    </div>
                    <!-- Total de Resultados Gratuidades -->
                    <div class="col-sm-6 col-md-4">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $evento->howManyWithResultsFree() }}</h3>

                                <p>Total de Resultados Gratuidades</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                        </div>
                    </div>
                    <!-- Total de Resultados Não Pagos -->
                    <div class="col-sm-6 col-md-4">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $evento->howManyWithResultsNotPaid() }}</h3>

                                <p>Total de Resultados Não Pagos</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                        </div>
                    </div>
                @endif

                @php($bigger_tournament = $evento->getTournamentWithMoreRegistrations())
                <!-- Maior Torneio -->
                <div class="col-sm-12 col-md-8">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{ $bigger_tournament['status'] ? $bigger_tournament['tournament']->name : $bigger_tournament['tournament'] }}
                            </h3>

                            <p>Maior Torneio</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-award"></i>
                        </div>
                        <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                    </div>
                </div>
                <!-- Maior Torneio Total -->
                <div class="col-sm-12 col-md-4">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{ $bigger_tournament['total'] }}</h3>

                            <p>Total de Inscritos no Maior Torneio</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-award"></i>
                        </div>
                        <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                    </div>
                </div>


            </div>
            <div class="row">
                <!-- Total de Emparceiramentos (Importados) -->
                <div class="col-sm-12 col-md-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{ $evento->quantosEmparceiramentos() }}</h3>

                            <p>Total de Emparceiramentos</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                    </div>
                </div>
                <!-- Total de Clubes Presentes  -->
                <div class="col-sm-12 col-md-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{ $evento->quantosClubes() }}</h3>

                            <p>Total de Clubes/Escolas/Instituições/Equipes Presentes</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
