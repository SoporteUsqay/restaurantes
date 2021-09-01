<?php include 'Application/Views/template/header.php'; ?>
<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>

    <div class="container">
        <br><br><br><br>

        <p class="text-center">
            <button class="btn btn-default" data-toggle="modal" data-target="#loginModal">Login</button>
        </p>

        <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="Login" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="loginForm" method="post" class="form-horizontal">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">Login</h4>
                        </div>

                        <div class="modal-body">
                            <strong>DNI</strong>
                            <div class="form-group">
                                <div class="col-xs-6">
                                    <input required="true" type="text" name="dni" type="text" placeholder="Ingrese el número de DNI" class="form-control" maxlength="8"/>
                                </div>
                            </div>
                            <strong>Nombre</strong>
                            <div class="form-group">                                
                                <div class="col-xs-6">
                                    <input required="true" type="text" name="nombre" type="text" placeholder="Ingrese el Nombre" class="form-control"/>
                                </div>
                            </div>
                            <strong>Username</strong>
                            <div class="form-group">                                
                                <div class="col-xs-5">
                                    <input type="text" class="form-control" name="username" required="true"/>
                                </div>
                            </div>
                            <strong>E-mail</strong>
                            <div class="form-group">                                
                                <div class="col-xs-5">
                                    <input type="text" class="form-control" name="mail" placeholder="ejemplo@mail.com"/>
                                </div>
                            </div>
                            <strong>Combo</strong>
                            <div class="form-group">                                
                                <div class="col-xs-5">
                                    <select   required="true" class="form-control" name="estado" id="estado">
                                        <option value="">Seleccione Opción</option>
                                        <option value="0">Habilitado</option>
                                        <option value="1">Inhabilitado</option>
                                    </select>
                                </div>
                            </div>                            
                            <strong>Password</strong>
                            <div class="form-group">
                                <div class="col-xs-5">
                                    <input required="true" type="password" class="form-control" name="password" />
                                </div>
                            </div>

                            <!--                            <div class="form-group">
                                                            <div class="col-xs-5 col-xs-offset-3" style="float: right">
                                                                <button type="submit" class="btn btn-primary">Login</button>
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>-->

                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Login</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('#loginForm').formValidation({
                    framework: 'bootstrap',
                    excluded: ':disabled',
                    icon: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-ban-circle',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    locale: 'es_ES',
                    fields: {
                        dni: {
                            validators: {
                                regexp: {
                                    regexp: /^[0-9]+$/,
                                    message: 'Ingrese sólo números'
                                    //,
                                    //                                    message: 'The username can only consist of alphabetical, number, dot and underscore'
                                },
                                stringLength: {
                                    min: 8,
                                    max: 8,
                                    message: 'Debe Conetener 8 caracteres'
                                    
                                    //                                    min: 6,
                                    //                                    max: 30,
                                    //                                    message: 'The username must be more than 6 and less than 30 characters long'
                                }
                            }                            
                        },
                        mail: {
                            validators: {
                                regexp: {
                                    regexp: /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/
                                }
                            }
                        },
                        nombre: {
                            validators: {
                                regexp: {
                                    regexp: /^[a-zA-Z\s]+$/,
                                    message: 'Ingrese sólo Letras'
                                }
                            }                            
                        },
                        username: {
                            validators: {
                                stringLength: {
                                    min: 8,
                                    max: 30                                    
                                    //                                    min: 6,
                                    //                                    max: 30,
                                    //                                    message: 'The username must be more than 6 and less than 30 characters long'
                                },
                                regexp: {
                                    regexp: /^[a-zA-Z0-9_\.]+$/
                                    //,
                                    //                                    message: 'The username can only consist of alphabetical, number, dot and underscore'
                                }
                            }                            
                        }
                    }
                });
            });
            
            $('#loginModal').on('hidden.bs.modal', function() {
                $('#loginForm').formValidation('resetForm', true);
            });
        </script>

    </div>
</body>