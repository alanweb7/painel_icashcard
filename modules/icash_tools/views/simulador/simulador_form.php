<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    #resultadoSimulacao {
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 25px 20px;
        font-size: 15px;
        line-height: 1.8;
        min-height: 300px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    #resultadoSimulacao p {
        margin-bottom: 20px;
    }

    #resultadoSimulacao span {
        font-weight: bold;
        color: #333;
    }

    /* STYLE DO FORM */

    .d-none {
        display: none;
    }

    .is-invalid {
        border-color: #dc3545;
        background-color: #fff5f5;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body panel-table-full">

                        <div id="form-etapas-container">
                            <div id="etapa-1" class="etapa-form">
                                <?php $this->load->view('simulador/simulador_form_t1'); ?>
                            </div>

                            <div id="etapa-2" class="etapa-form d-none">
                                <?php $this->load->view('simulador/simulador_form_t2'); ?>
                            </div>

                            <div id="etapa-3" class="etapa-form d-none">
                                <?php $this->load->view('simulador/simulador_form_t3'); ?>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>

        <?php $this->load->view('simulador/simulador_modal_valores'); ?>
    </div>
</div>

<?php init_tail(); ?>

<!-- ação para simular os valores -->
<script>
    // $(function() {
    //     $('#valor_liquido').click(function() {
    //         $('#tipo_valor').val('liquido');
    //         $(this).addClass('active');
    //         $('#valor_limite').removeClass('active');
    //     });

    //     $('#valor_limite').click(function() {
    //         $('#tipo_valor').val('limite');
    //         $(this).addClass('active');
    //         $('#valor_liquido').removeClass('active');
    //     });

    //     // Simulação mockada ao sair do campo "valor"
    //     $('#valor').on('blur', function() {
    //         const valorInformado = parseFloat($(this).val().replace(/\D/g, '')) || 0;

    //         $('#valor_liberado').text((valorInformado * 0.85).toFixed(2));
    //         $('#valor_parcela').text((valorInformado / 12).toFixed(2));
    //         $('#prazo_info').text($('#prazo').val() || '-');
    //         $('#limite_utilizado').text(valorInformado.toFixed(2));
    //     });
    // });
</script>


<!-- CALCULA AS PARCELAS AO CLICAR EM LIMITE OU LÍQUIDO -->
<script>
    $(document).ready(function() {
        $('.type-operator-control').on('click', function() {
            const tipo = $(this).data('type'); // liquido ou limite
            const valor = parseFloat($('#valor').val());

            if (!valor || isNaN(valor)) {
                alert('Informe um valor válido.');
                return;
            }

            console.log('Tipo selecionado:', tipo);

            $.post(admin_url + 'icash_tools/simulador/consultar', {
                valor: valor,
                tipo: tipo
            }, function(response) {
                console.log('Simulando...');
                console.log(response);

                if (response.success) {
                    let html = '';

                    response.resultados.forEach(function(item) {
                        html += `
                        <tr>
                            <td>
                                <button class="btn btn-sm btn-success selecionar-parcela"
                                    data-prazo="${item.parcelas}"
                                    data-total="${item.valor_total}"
                                    data-parcela="${item.valor_parcela}"
                                    data-liberado="${parseFloat(response.valor_base).toFixed(2)}"
                                >
                                    Selecionar
                                </button>
                            </td>
                            <td>em ${item.parcelas}x</td>
                            <td>${item.valor_total}</td>
                            <td>${item.valor_parcela}</td>
                            <td>${parseFloat(response.valor_base).toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</td>
                        </tr>
                    `;
                    });

                    $('#tabela-simulador').html(html);
                    $('#modalSimulador').modal('show');
                } else {
                    $('#resultado-simulacao').html(`<span class="text-danger">${response.message}</span>`);
                }
            }, 'json');
        });
    });
</script>


<!-- POPULAR OS VALORES DA SIMULACAO  -->
<script>
    $(document).on('click', '.selecionar-parcela', function() {
        const prazo = $(this).data('prazo');
        const parcela = $(this).data('parcela');
        const total = $(this).data('total');
        const liberado = $(this).data('liberado');

        // Atualizar botão do rodapé
        $('#btn-parcela-alerta')
            .removeClass('btn-warning')
            .addClass('btn-success')
            .text(`${prazo}x de R$ ${parcela} selecionado (Total: R$ ${total})`);

        // Fechar o modal
        $('#modalSimulador').modal('hide');


        // atualizar campos hidden
        $('input[name="valor_liberado"]').val(liberado);
        $('input[name="valor_parcela"]').val(parcela);
        $('input[name="prazo_info"]').val(prazo);
        $('input[name="limite_utilizado"]').val(total);

        // Atualizar os campos do card lateral
        $('#valor_liberado').text(liberado.toString().replace('.', ','));
        $('#valor_parcela').text(parcela);
        $('#prazo_info').text(`${prazo}x`);
        $('#limite_utilizado').text(total);
        // $('#limite_utilizado').text(Number(total).toLocaleString('pt-BR', {
        //     minimumFractionDigits: 2
        // }));

        console.log('Dados selecionados');
        // Se quiser mostrar os dados em algum outro lugar do sistema, faça aqui.
        console.log({
            prazo: prazo,
            valor_parcela: parcela,
            total: total,
            valor_liberado: liberado
        });

        // Aqui você pode salvar num hidden, ou preencher outro form, etc.


    });
</script>

<script>
    let currentStep = 1;
    const totalSteps = 3;

    function showStep(step) {
        $('.form-step').addClass('d-none');
        $('#step-' + step).removeClass('d-none');
    }

    function goToNextStep() {
        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
        }
    }

    function goToPreviousStep() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    }

    // Exemplo de uso com botões:
    $(document).on('click', '#btn-next', function() {
        goToNextStep();
    });

    $(document).on('click', '#btn-prev', function() {
        goToPreviousStep();
    });
</script>

<script>
    let etapaAtual = 1;

    function mostrarEtapa(num) {
        $('.etapa-form').removeClass('active').addClass('d-none');
        $('#etapa-' + num).removeClass('d-none').addClass('active');
        etapaAtual = num;
    }

    $(document).on('click', '#btn-next', function() {
        if (etapaAtual < 3) {
            mostrarEtapa(etapaAtual + 1);
        }
    });

    $(document).on('click', '#btn-prev', function() {
        if (etapaAtual > 1) {
            mostrarEtapa(etapaAtual - 1);
        }
    });

    // Ao enviar o último form
    $(document).on('submit', '#form_etapa_3', function(e) {
        e.preventDefault();

        const data = {
            ...Object.fromEntries(new FormData($('#simuladorForm')[0])),
            ...Object.fromEntries(new FormData($('#form_etapa_2')[0]))
        };
        // const data = {};

        console.log("Dados capturados...");
        console.log(data);

        $.post(admin_url + 'icash_tools/simulador/add_proposal', data, function(res) {
            console.log(res);
            if (res.success) {
                alert('Enviado com sucesso!');
            }
            // window.location.href = '...'; // se quiser redirecionar
        });
    });

    // Inicializa na primeira etapa
    $(document).ready(function() {
        mostrarEtapa(1);
    });
</script>
<!-- validar campos antes de avanças -->
<script>
    $('.next-2').on('click', function() {
        console.log('[DEBUG] Botão .next-2 clicado');

        let isValid = true;

        // $('#form_etapa_2 [required]').each(function() {
        //     if (!$(this).val()) {
        //         isValid = false;
        //         $(this).addClass('is-invalid');

        //         // Foca no primeiro inválido e para o loop
        //         $(this).focus();
        //         return false;
        //     } else {
        //         $(this).removeClass('is-invalid');
        //     }
        // });

        // if (!isValid) {
        //     console.warn('[AVISO] Campos obrigatórios não preenchidos.');
        //     return false;
        // }

        // console.log('[DEBUG] Todos os campos obrigatórios estão preenchidos.');


        // preenche os dados no resumo
        preencherResumo();
        // Avançar
        $('#form_etapa_2').closest('.etapa').hide();
        $('#form_etapa_3').closest('.etapa').show();
    });
</script>

<!-- busca no via cep -->
<script>
    $(function() {
        $('#cep').on('blur', function() {
            console.log('cep...');
            var cep = $(this).val().replace(/\D/g, '');
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            $('#rua').val(data.logradouro);
                            $('#cidade').val(data.localidade);
                            $('#uf').val(data.uf);
                        }
                    });
            }
        });
    });
</script>



<!-- validar campos -->
<!-- validar email -->
<script>
    const emailInput = document.getElementById('email');
    const feedback = document.getElementById('email-feedback');

    function validarEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    emailInput.addEventListener('input', () => {
        const email = emailInput.value;

        if (email === '') {
            feedback.textContent = ''; // limpa mensagem se vazio
            emailInput.classList.remove('is-invalid');
            emailInput.classList.remove('is-valid');
            return;
        }

        if (validarEmail(email)) {
            feedback.textContent = '';
            emailInput.classList.remove('is-invalid');
            emailInput.classList.add('is-valid'); // para feedback visual com bootstrap
        } else {
            feedback.textContent = 'Por favor, insira um email válido.';
            emailInput.classList.add('is-invalid');
            emailInput.classList.remove('is-valid');
        }
    });
</script>

<!-- copiar valores dos campos conforme a selecao -->
<script>
    document.getElementById('tipo_chave').addEventListener('change', function() {
        const tipo = this.value;

        const chavePixInput = document.getElementById('chave_pix');
        const cpf = document.getElementById('cpf').value ?? '';
        const email = document.getElementById('email').value ?? '';
        const telefone = document.getElementById('telefone').value ?? '';

        console.log(telefone);

        console.log("Tipo: " + tipo);
        if (tipo === 'cpf') {
            chavePixInput.value = cpf;
        } else if (tipo === 'email') {
            chavePixInput.value = email;
        } else if (tipo === 'telefone') {
            chavePixInput.value = telefone;
        } else {
            chavePixInput.value = ''; // limpa se for aleatória ou não selecionado
        }
    });
</script>

<!-- POPULAR OS CAMPOS DO RESUMO -->
<script>
    function preencherResumo() {
        // Dados Pessoais
        $('#resumo_cliente').text($('input[name="nome_completo"]').val());
        $('#resumo_cpf').text($('input[name="cpf"]').val());
        $('#resumo_rg').text($('input[name="rg"]').val());
        $('#resumo_nascimento').text($('input[name="data_nascimento"]').val());

        // Endereço completo
        let endereco = `${$('#rua').val()}, ${$('input[name="numero"]').val()} - ${$('input[name="setor"]').val()}, ${$('#cidade').val()} - ${$('#uf').val()} | CEP: ${$('#cep').val()}`;
        $('#resumo_endereco').text(endereco);

        // Contato
        $('#resumo_email').text($('input[name="email"]').val());
        $('#resumo_telefone').text($('input[name="telefone"]').val());

        // Dados da proposta
        $('#resumo_credenciadora').text($('#credenciadora').val());
        $('#resumo_tabela').text($('#tabela').val());
        $('#resumo_prazo').text($('#prazo').val());
        $('#resumo_valor_bruto').text($('#valor_bruto').val());
        $('#resumo_valor_liquido').text($('#valor_liquido').val());
        $('#resumo_valor_parcela').text($('#valor_parcela').val());

        // Dados bancários
        $('#resumo_banco').text($('#banco').val());
        $('#resumo_agencia').text($('#agencia').val());
        $('#resumo_conta').text($('#conta').val());
        $('#resumo_tipo_pix').text($('#tipo_chave option:selected').text());
        $('#resumo_chave_pix').text($('#chave_pix').val());
    }
</script>


<!-- mascaras -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function() {
        $('input[name="cpf"]').mask('000.000.000-00', {
            reverse: true
        });
        $('input[name="cep"]').mask('00000-000', {
            reverse: true
        });
        $('input[name="telefone"]').mask('(00) 00000-0000');
    });
</script>


</body>

</html>