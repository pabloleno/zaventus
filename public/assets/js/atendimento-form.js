(() => {
    'use strict';
    const d = window.atendimentoDados;
    const el = id => document.getElementById(id);
    const form = el('form-atendimento');
    if (!form || !d) return;
    let itens = (d.ordem.itens || []).map(i => ({...i}));
    let parcelas = (d.ordem.parcelas || []).map(p => ({...p}));
    let totais = null, timer, sequencia = 0, ultimoCalculo = Promise.resolve(false);
    const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const num = v => Number(String(v ?? '0').includes(',') ? String(v).replace(/\./g, '').replace(',', '.') : v) || 0;
    const moeda = v => new Intl.NumberFormat('pt-BR', {style:'currency',currency:'BRL'}).format(num(v));
    const options = (map, current) => Object.entries(map).map(([value, label]) => '<option value="'+esc(value)+'"'+(String(current) === value ? ' selected' : '')+'>'+esc(label)+'</option>').join('');
    const formasParcela = p => {
        const formas = Object.fromEntries(d.formas.map(f => [f, f]));
        if (p.id_parcela && p.forma_de_pagamento && !d.formas.includes(p.forma_de_pagamento)) {
            formas[p.forma_de_pagamento] = p.forma_de_pagamento + ' (inativa — plano existente)';
        }
        return options(formas, p.forma_de_pagamento);
    };
    const arte = {nao_necessita:'Não necessita arte',cliente:'Cliente já possui arte',grafica:'Gráfica irá produzir'};
    const execucao = {interna:'Interna',externa:'Externa',mista:'Interna + Externa'};
    function campo(label, key, value, extra='') {
        return '<label class="small d-block">'+esc(label)+'<input class="form-control form-control-sm" data-campo="'+key+'" value="'+esc(value)+'" inputmode="decimal" '+extra+'></label>';
    }
    function renderItens() {
        el('at-vazio').hidden = itens.length > 0;
        el('at-itens').innerHTML = itens.map((i, n) => {
            const cortesia = Number(i.cortesia) === 1;
            return '<div class="border rounded p-3 mb-3 at-item" data-index="'+n+'">'+
                '<div class="d-flex justify-content-between align-items-start mb-2"><div><strong>'+esc(i.nome)+'</strong> '+(cortesia?'<span class="badge badge-success">Cortesia — sem custo</span>':'')+
                '<div class="small text-muted">'+esc(i.descricao || '')+'</div></div><button type="button" class="btn btn-outline-danger btn-sm at-remove" aria-label="Remover '+esc(i.nome)+'">Remover</button></div>'+
                '<div class="row"><div class="col-md-3"><label class="small d-block">Precificação<select class="form-control form-control-sm" data-campo="tipo_preco">'+options(d.tipos,i.tipo_preco||'unidade')+'</select></label></div>'+
                '<div class="col-md-2">'+campo('Quantidade','quantidade',i.quantidade||1)+'</div>'+
                '<div class="col-md-2"><label class="small d-block">Unidade<input class="form-control form-control-sm" data-campo="unidade" value="'+esc(i.unidade||'un')+'" maxlength="16"></label></div>'+
                '<div class="col-md-3">'+campo(cortesia?'Valor cobrado (R$)':'Preço da métrica (R$)','valor',cortesia?'0.00':i.valor,cortesia?'readonly':'')+(cortesia?'<small class="text-muted">Preço normal: '+moeda(i.valor_catalogo)+'</small>':'')+'</div>'+
                '<div class="col-md-2"><span class="small d-block">Total do item</span><strong class="at-item-total d-block pt-2">'+moeda(i.total||0)+'</strong></div></div>'+
                '<details class="at-medidas mb-2" '+((i.largura || i.altura || ['metro_linear','metro_quadrado'].includes(i.tipo_preco))?'open':'')+'><summary class="small mb-2">Medidas</summary><div class="row">'+
                '<div class="col-md-3">'+campo('Largura / comprimento','largura',i.largura||'')+'</div><div class="col-md-3">'+campo('Altura','altura',i.altura||'')+'</div>'+
                '<div class="col-md-3"><label class="small d-block">Unidade das medidas<select data-campo="unidade_dimensao" class="form-control form-control-sm">'+options({m:'Metro (m)',cm:'Centímetro (cm)',mm:'Milímetro (mm)'},i.unidade_dimensao||'m')+'</select></label></div>'+
                '<div class="col-md-3"><span class="small">Área por peça</span><strong class="at-item-area d-block pt-2">—</strong></div></div></details>'+
                '<div class="row"><div class="col-md-5"><label class="small d-block">Arte<select data-campo="arte" class="form-control form-control-sm">'+options(arte,i.arte||'nao_necessita')+'</select></label></div>'+
                '<div class="col-md-4"><label class="small d-block">Execução<select data-campo="tipo_execucao" class="form-control form-control-sm">'+options(execucao,i.tipo_execucao||'interna')+'</select></label></div>'+
                '<div class="col-md-3 d-flex align-items-center"><label class="small"><input type="checkbox" data-campo="necessita_instalacao" '+(Number(i.necessita_instalacao)?'checked':'')+'> Necessita instalação</label></div></div></div>';
        }).join('');
        condicionais();
        agendarCalculo();
    }
    function lerItens() {
        el('at-itens').querySelectorAll('.at-item').forEach(card => {
            const item = itens[Number(card.dataset.index)];
            card.querySelectorAll('[data-campo]').forEach(input => item[input.dataset.campo] = input.type === 'checkbox' ? (input.checked?1:0) : input.value);
            if (Number(item.cortesia)) item.valor = '0.00';
        });
    }
    function adicionar(cortesia) {
        lerItens();
        const s = d.catalogo.find(s => String(s.id_servico) === el('at-catalogo').value);
        if (!s) { mostrarErro('Selecione um serviço do catálogo.'); return; }
        itens.push({id_servico_catalogo:s.id_servico,nome:s.nome,descricao:s.descricao,tipo_preco:s.tipo_preco,unidade:s.unidade,valor:cortesia?'0.00':s.valor,valor_catalogo:s.valor,quantidade:'1',largura:s.largura_padrao,altura:s.altura_padrao,unidade_dimensao:s.unidade_dimensao,tipo_execucao:s.tipo_execucao,arte:s.arte_padrao,necessita_instalacao:s.necessita_instalacao,cortesia:cortesia?1:0});
        renderItens();
    }
    function renderParcelas() {
        if (!parcelas.length) parcelas.push({forma_de_pagamento:d.formas[0]||'Dinheiro',data_de_vencimento:d.hoje,valor_da_parcela:totais?.total||'0.00',observacoes:''});
        el('at-parcelas').innerHTML = parcelas.map((p,n) => '<div class="row at-parcela align-items-end" data-index="'+n+'">'+
            '<div class="col-sm-5 form-group"><label class="small d-block">Forma de pagamento<select class="form-control form-control-sm" data-parcela="forma_de_pagamento">'+formasParcela(p)+'</select></label></div>'+
            '<div class="col-sm-3 form-group"><label class="small d-block">Vencimento<input type="date" class="form-control form-control-sm" data-parcela="data_de_vencimento" value="'+esc(p.data_de_vencimento||d.hoje)+'" required></label></div>'+
            '<div class="col-sm-3 form-group"><label class="small d-block">Valor (R$)<input class="form-control form-control-sm" data-parcela="valor_da_parcela" value="'+esc(p.valor_da_parcela||'0.00')+'" inputmode="decimal" '+(parcelas.length===1?'readonly':'')+'></label></div>'+
            '<div class="col-sm-1 form-group">'+(parcelas.length>1?'<button type="button" class="btn btn-outline-danger btn-sm at-remove-parcela" aria-label="Remover parcela">×</button>':'')+'</div></div>').join('');
        resumoPlano();
    }
    function lerParcelas() {
        el('at-parcelas').querySelectorAll('.at-parcela').forEach(row => row.querySelectorAll('[data-parcela]').forEach(input => parcelas[Number(row.dataset.index)][input.dataset.parcela]=input.value));
    }
    function condicionais() {
        const entrada = el('at-entrada').checked;
        el('at-entrada-necessaria').value = entrada?'1':'0';
        el('at-entrada-campo').hidden = !entrada;
        const desconto = el('at-desconto-tipo').value !== 'nenhum';
        el('at-desconto-campo').hidden = !desconto;
        el('at-desconto-motivo-campo').hidden = !desconto;
        const externa = itens.some(i => (i.tipo_execucao && i.tipo_execucao !== 'interna') || Number(i.necessita_instalacao));
        el('at-externa').hidden = !externa;
        const temArte = itens.some(i => i.arte === 'grafica');
        [...el('at-status').options].forEach(o => {
            o.hidden = ((!temArte && ['falta_arte','arte_aprovacao'].includes(o.value)) || (!externa && o.value==='instalacao_agendada')) && !o.selected;
        });
        if (totais) {
            const restante = Math.max(0,num(totais.total)-num(el('at-entrada-valor').value));
            el('at-entrada-saldo').textContent = 'Saldo após o recebimento da entrada: '+moeda(restante)+'.';
        }
    }
    function resumoPlano() {
        const soma = parcelas.reduce((s,p)=>s+num(p.valor_da_parcela),0);
        const ok = totais && Math.abs(soma-num(totais.total))<0.005;
        el('at-plano-resumo').textContent = 'Total combinado: '+moeda(soma)+(ok?'':' · Ajuste as parcelas para corresponder ao total.');
        el('at-plano-resumo').className = 'small mt-2 '+(ok?'text-muted':'text-danger');
    }
    function mostrarErro(mensagem='') {
        el('at-erro').textContent = mensagem;
        el('at-erro').hidden = !mensagem;
    }
    function serializar() {
        el('at-itens-json').value = JSON.stringify(itens);
        el('at-parcelas-json').value = JSON.stringify(parcelas);
    }
    function agendarCalculo() {
        clearTimeout(timer);
        sequencia++;
        el('at-salvar').disabled = true;
        timer = setTimeout(()=>{ ultimoCalculo=calcular(); },250);
    }
    async function calcular() {
        lerItens(); lerParcelas(); condicionais(); serializar();
        const atual = ++sequencia;
        if (!itens.length) { mostrarErro(); return false; }
        try {
            const response = await fetch('/ordensDeServicos/calcularAtendimento', {method:'POST',body:new FormData(form),headers:{'X-Requested-With':'XMLHttpRequest'}});
            const data = await response.json();
            if (atual !== sequencia) return false;
            if (!response.ok) throw new Error(data.erro || 'Não foi possível calcular os valores.');
            totais = data;
            el('at-desconto-valor').value=data.desconto_informado;
            el('at-subtotal').textContent=moeda(data.subtotal);
            el('at-desconto').textContent=moeda(data.desconto);
            el('at-total').textContent=moeda(data.total);
            el('at-itens').querySelectorAll('.at-item').forEach((card,n)=>{
                card.querySelector('.at-item-total').textContent=moeda(data.itens[n].total);
                card.querySelector('.at-item-area').textContent=data.itens[n].area ? new Intl.NumberFormat('pt-BR',{maximumFractionDigits:4}).format(Number(data.itens[n].area))+' m²':'—';
            });
            if (parcelas.length===1) {
                parcelas[0].valor_da_parcela=data.total;
                el('at-parcelas').querySelector('[data-parcela="valor_da_parcela"]').value=data.total;
            }
            condicionais(); resumoPlano(); serializar(); mostrarErro();
            el('at-salvar').disabled=false;
            return true;
        } catch(error) {
            if (atual===sequencia) { mostrarErro(error.message || 'Confira sua conexão e tente novamente.'); el('at-salvar').disabled=true; }
            return false;
        }
    }
    function clienteAtual() { return d.clientes.find(c=>String(c.id_cliente)===el('at-cliente').value); }
    function endereco(c) { return [c.logradouro,c.numero,c.complemento,c.bairro,c.municipio,c.UF,c.cep].filter(Boolean).join(', '); }
    function atualizarCliente() {
        const c=clienteAtual();
        el('at-cliente-resumo').textContent=c ? [c.cpf||c.cnpj,c.whatsapp||c.celular,c.email,endereco(c)].filter(Boolean).join(' · '):'';
    }
    el('at-add-servico').addEventListener('click',()=>adicionar(false));
    el('at-add-brinde').addEventListener('click',()=>adicionar(true));
    el('at-itens').addEventListener('click',event=>{if(event.target.closest('.at-remove')){lerItens();itens.splice(Number(event.target.closest('.at-item').dataset.index),1);renderItens();}});
    el('at-itens').addEventListener('input',()=>{lerItens();condicionais();agendarCalculo();});
    el('at-itens').addEventListener('change',event=>{lerItens();if(event.target.dataset.campo==='tipo_preco' && event.target.value.startsWith('metro_')) event.target.closest('.at-item').querySelector('details').open=true;condicionais();agendarCalculo();});
    el('at-add-parcela').addEventListener('click',()=>{lerParcelas();parcelas.push({forma_de_pagamento:d.formas[0]||'Dinheiro',data_de_vencimento:d.hoje,valor_da_parcela:'0.00'});renderParcelas();});
    el('at-parcelas').addEventListener('input',()=>{lerParcelas();resumoPlano();serializar();});
    el('at-parcelas').addEventListener('click',event=>{if(event.target.closest('.at-remove-parcela')){lerParcelas();parcelas.splice(Number(event.target.closest('.at-parcela').dataset.index),1);renderParcelas();agendarCalculo();}});
    ['at-desconto-tipo','at-desconto-valor','at-frete','at-outros'].forEach(id=>el(id).addEventListener('input',()=>{condicionais();agendarCalculo();}));
    el('at-entrada').addEventListener('change',condicionais);
    el('at-entrada-valor').addEventListener('input',condicionais);
    el('at-cliente').addEventListener('change',atualizarCliente);
    if (window.jQuery) window.jQuery(el('at-cliente')).on('change',atualizarCliente);
    el('at-usar-endereco').addEventListener('click',()=>{const c=clienteAtual();if(c)el('at-endereco').value=endereco(c);else mostrarErro('Selecione o cliente antes de copiar o endereço.');});
    form.addEventListener('submit',async event=>{
        event.preventDefault(); clearTimeout(timer);
        el('at-salvar').disabled=true;
        const ok=await calcular();
        if (!ok || !form.reportValidity()) return;
        lerParcelas();serializar();
        el('at-salvar').disabled=true;
        el('at-salvando').textContent='Salvando orçamento…';
        HTMLFormElement.prototype.submit.call(form);
    });
    renderParcelas();renderItens();atualizarCliente();condicionais();
})();
