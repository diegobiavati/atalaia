<li class="list-group-item justify-content-between align-items-center menu-list-01">
    <a id="visao-geral-gaviao" href="javascript: void(0);">                
        <i class="ion-ios-eye"></i>
        Visão geral
        <span class="badge badge-primary badge-pill"></span>
    </a>
</li>
<li class="list-group-item justify-content-between align-items-center menu-list-01">
    <a id="anos-de-formacao" href="javascript: void(0);">
        <i class="ion-ios-calendar-outline"></i> 
        Anos de formação
        <span class="badge badge-primary badge-pill"></span>
    </a>
</li>
<li class="list-group-item justify-content-between align-items-center menu-list-01">
    <a id="gerenciar-operadores-gaviao" href="javascript: void(0);">
        <i class="ion-ios-people"></i> 
        Gerenciar Operadores
        <span class="badge badge-primary badge-pill"></span>
    </a>
</li>
<li class="list-group-item justify-content-between align-items-center menu-list-01">
    <a id="alunos-gaviao" href="javascript: void(0);">
        <i class="ion-android-contacts"></i>
        Gerenciar Alunos
        <span class="badge badge-primary badge-pill"></span>
    </a>                
</li>
<li class="list-group-item justify-content-between align-items-center menu-list-01">
    <a id="viewLancamentos" href="javascript: void(0);">
        <i class="ion-paper-airplane"></i>
        FO / FATD
        <span class="badge badge-primary badge-pill"></span>
    </a>
</li>
<li class="list-group-item justify-content-between align-items-center menu-list-01">
    <a id="view-relatorios" href="javascript: void(0);">                
        <i class="ion-ios-pie"></i>
        Documentação e relatórios
        <span class="badge badge-primary badge-pill"></span>
    </a>
</li>
@if(session()->has('qms_selecionada') && session()->get('qms_selecionada') == 9999)
    <li class="list-group-item justify-content-between align-items-center menu-list-01">
        <a id="view-diploma" href="javascript: void(0);">                
            <i class="ion-university"></i>
            Diploma Digital
            <span class="badge badge-primary badge-pill"></span>
        </a>
    </li>
@endif