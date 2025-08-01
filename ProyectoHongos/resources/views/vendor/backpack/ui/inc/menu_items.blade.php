<li class="nav-item"><a class="nav-link" href="{{ backpack_url("dashboard") }}"><i class="la la-home nav-icon"></i> {{ trans("backpack::base.dashboard") }}</a></li>

<x-backpack::menu-item title="Salas cultivos" icon="la la-question" :link="backpack_url('salas-cultivo')" />
<x-backpack::menu-item title="Procesos esterilizacions" icon="la la-question" :link="backpack_url('procesos-esterilizacion')" />
<x-backpack::menu-item title="Inventario laboratorios" icon="la la-question" :link="backpack_url('inventario-laboratorio')" />
<x-backpack::menu-item title="Solicitudes procuradurias" icon="la la-question" :link="backpack_url('solicitudes-procuraduria')" />
<x-backpack::menu-item title="Sustratos" icon="la la-question" :link="backpack_url('sustratos')" />
<x-backpack::menu-item title="Tipos contaminacions" icon="la la-question" :link="backpack_url('tipos-contaminacion')" />
<x-backpack::menu-item title="Cepas" icon="la la-question" :link="backpack_url('cepas')" />
<x-backpack::menu-item title="Lotes inoculos" icon="la la-question" :link="backpack_url('lotes-inoculo')" />
<x-backpack::menu-item title="Registros ambientales" icon="la la-question" :link="backpack_url('registros-ambientales')" />
<x-backpack::menu-item title="Lotes produccions" icon="la la-question" :link="backpack_url('lotes-produccion')" />
<x-backpack::menu-item title="Lote sustratos" icon="la la-question" :link="backpack_url('lote-sustratos')" />
<x-backpack::menu-item title="Unidades produccions" icon="la la-question" :link="backpack_url('unidades-produccion')" />
<x-backpack::menu-item title="Cosechas" icon="la la-question" :link="backpack_url('cosechas')" />
<x-backpack::menu-item title="Roles" icon="la la-question" :link="backpack_url('roles')" />
<x-backpack::menu-item title="Permisos" icon="la la-question" :link="backpack_url('permisos')" />
<x-backpack::menu-item title="Bitacora actividades" icon="la la-question" :link="backpack_url('bitacora-actividades')" />
<x-backpack::menu-item title="Acciones" icon="la la-question" :link="backpack_url('acciones')" />