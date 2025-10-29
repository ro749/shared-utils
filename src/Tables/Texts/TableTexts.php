<?php

namespace Ro749\SharedUtils\Tables\Texts;

class TableTexts
{
    public string $decimal;
    public string $emptyTable;
    public string $info;
    public string $infoEmpty;
    public string $infoFiltered;
    public string $infoPostFix;
    public string $thousands;
    public string $lengthMenu;
    public string $loadingRecords;
    public string $processing;
    public string $search;
    public string $zeroRecords;

    public Paginate $paginate;
    public Aria $aria;

    public function __construct(
        string $decimal = '',
        string $emptyTable = 'Ningun registro encontrado',
        string $info = 'Mostrando _START_ a _END_ de _TOTAL_ registros',
        string $infoEmpty = 'Mostrando 0 a 0 de 0 registros',
        string $infoFiltered = ' (filtrado de _MAX_ registros)',
        string $infoPostFix = '',
        string $thousands = ',',
        string $lengthMenu = 'Mostrar _MENU_ registros',
        string $loadingRecords = 'Cargando...',
        string $processing = '',
        string $search = 'Buscar:',
        string $zeroRecords = 'Ningun registro encontrado que coincida con su busqueda.',
        Paginate $paginate = null,
        Aria $aria = null
    )  {
        $this->decimal = $decimal;
        $this->emptyTable = $emptyTable;
        $this->info = $info;
        $this->infoEmpty = $infoEmpty;
        $this->infoFiltered = $infoFiltered;
        $this->infoPostFix = $infoPostFix;
        $this->thousands = $thousands;
        $this->lengthMenu = $lengthMenu;
        $this->loadingRecords = $loadingRecords;
        $this->processing = $processing;
        $this->search = $search;
        $this->zeroRecords = $zeroRecords;

        $this->paginate = $paginate ?? new Paginate();
        $this->aria = $aria ?? new Aria();
    }
}