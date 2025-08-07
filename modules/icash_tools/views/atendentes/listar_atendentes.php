<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $module_assets_url = module_dir_url('icash_tools', 'assets/js/'); ?>
<script src="<?= $module_assets_url; ?>icash_tools_atendentes_functions_js.js?ver=7.7.5"></script>
<style>
    .button_send {
        float: right;
    }

    /* style for toggle */

    .switch {
        position: relative;
        display: inline-block;
        width: 34px;
        height: 20px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 20px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #4caf50;
    }

    input:checked+.slider:before {
        transform: translateX(14px);
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">Lista de Atendentes</h4>
                        <hr>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addStaffModal">
                            <i class="fa fa-plus"></i> Novo Atendente
                        </button>
                        <br><br>
                        <table class="table dt-table table-striped">
                            <thead>
                                <tr>
                                    <th>Nome Completo</th>
                                    <th>Telefone</th>
                                    <th>CPF</th>
                                    <th>Email</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                function formatar_cpf($cpf)
                                {
                                    $cpf = preg_replace('/\D/', '', $cpf); // Remove não numéricos

                                    if (strlen($cpf) == 11) {
                                        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $cpf);
                                    }

                                    return $cpf; // Retorna como está se não tiver 11 dígitos
                                }
                                foreach ($staffs as $staff) :
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($staff['firstname']) ?></td>
                                        <td><?= htmlspecialchars($staff['phonenumber']) ?></td>
                                        <td><?= formatar_cpf(htmlspecialchars($staff['cpf_cnpj'])) ?></td>
                                        <td><?= htmlspecialchars($staff['email']) ?></td>
                                        <td>
                                            <?php
                                            $jsonStaff = json_encode($staff);
                                            // var_dump($jsonStaff);
                                            ?>
                                            <a href="#" class="btn btn-success btn-icon" style="color:#fff" onclick="editStaff(<?= htmlspecialchars($jsonStaff, ENT_QUOTES, 'UTF-8') ?>)">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <!-- Toggle para ativar/desativar -->
                                            <label class="switch">
                                                <input type="checkbox"
                                                    onchange="toggleStaffStatus(<?= $staff['staffid'] ?>, this.checked)"
                                                    <?= $staff['active'] ? 'checked' : '' ?>>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addStaffModal" tabindex="-1" role="dialog" aria-labelledby="addStaffModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="panel-body">
                <h4 class="no-margin"><?php echo _l('Registro de Atendente'); ?></h4>
                <hr>
                <?php echo form_open('icash_tools/register_staff/submit', ['id' => 'register_staff_form']); ?>
                <div class="form-group">
                    <label for="fullname"><?php echo _l('Nome completo'); ?></label>
                    <input type="text" id="fullname" name="fullname" class="form-control" required>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="phone"><?php echo _l('Telefone'); ?></label>
                            <input type="text" id="phone" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="cpf"><?php echo _l('CPF'); ?></label>
                            <input type="text" id="cpf" name="cpf" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email"><?php echo _l('Email'); ?></label>
                    <input type="email" id="email" name="email" class="form-control" value="" required>
                </div>
                <div class="form-group">
                    <label for="password"><?php echo _l('Senha'); ?></label>
                    <div class="input-group">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            autocomplete="new-password"
                            required
                            aria-describedby="generate-password-btn">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group-append" style="margin-top: 15px;">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            id="toggle-password-visibility"
                            tabindex="-1">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary"
                            id="generate-password-btn"
                            tabindex="-1">
                            <?php echo _l('Gerar senha'); ?>
                        </button>
                    </div>
                </div>
                <div class="button_send">
                    <button type="submit" class="btn btn-success"><?php echo _l('Registrar'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editStaffModal" tabindex="-1" role="dialog" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php echo form_open('icash_tools/register_staff/update_staff', ['id' => 'editStaffForm']); ?>
            <div class="modal-header">
                <h5 class="modal-title" id="editStaffModalLabel">Editar Funcionário</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="staffEmail">Email</label>
                    <input type="email" class="form-control" id="staffEmail" name="email" required>
                </div>
                <div class="form-group">
                    <label for="fullname">Nome</label>
                    <input type="text" class="form-control" id="staffFullname" name="fullname" required>
                    <input type="hidden" name="action" value="editStaff">
                    <input type="hidden" name="staffid" id="staffId">
                    <input type="hidden" name="role" id="role">
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="phone"><?php echo _l('Telefone'); ?></label>
                            <input type="text" id="staffphone" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="cpf"><?php echo _l('CPF'); ?></label>
                            <input type="text" id="staffcpf" name="cpf" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password"><?php echo _l('Senha'); ?></label>
                    <div class="input-group">
                        <input
                            type="password"
                            id="rstpassword"
                            name="password"
                            class="form-control"
                            autocomplete="new-password"
                            aria-describedby="generate-password-btn">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-secondary" id="toggle-rst-password-visibility" tabindex="-1">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-primary" id="rst-password-btn" tabindex="-1">
                                <?php echo _l('Gerar senha'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<script>
    // Toggle password visibility
    document.getElementById('toggle-password-visibility').addEventListener('click', function() {
        const passwordField = document.getElementById('password');
        const icon = this.querySelector('i');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    document.getElementById('toggle-rst-password-visibility').addEventListener('click', function() {
        const passwordField = document.getElementById('rstpassword');
        const icon = this.querySelector('i');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Generate random password
    document.getElementById('generate-password-btn').addEventListener('click', function() {
        const passwordField = document.getElementById('password');
        const rstpasswordField = document.getElementById('rstpassword');
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
        let password = "";
        for (let i = 0; i < 12; i++) { // 12-character password
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        passwordField.value = password;
        rstpasswordField.value = password;
    });
    document.getElementById('rst-password-btn').addEventListener('click', function() {
        const rstpasswordField = document.getElementById('rstpassword');
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
        let password = "";
        for (let i = 0; i < 12; i++) { // 12-character password
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        rstpasswordField.value = password;
    });
</script>
<?php init_tail(); ?>

<script>
    $(function() {
        $('#cpf, #staffcpf').on('input', function() {
            var cpf = $(this).val().replace(/\D/g, '');

            if (cpf.length > 11) cpf = cpf.substring(0, 11);

            cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
            cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
            cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');

            $(this).val(cpf);
        });
    });
</script>