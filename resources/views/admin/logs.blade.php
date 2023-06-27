@extends('layouts.admin.admin_dashboardapp')

@section("content")
    <div class="container">
        <div class="container mt--6">
            <div class="row">
                <div class="col-xl-12">

                    <div class="card">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col text-center">
                                    <h3 class="mb-0"><i class="fa fa-file"></i>Log Files</h3>
                                </div>

                            </div>
                        </div>
                        <div class="container">
                            <hr>

                        </div>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane  active" id="allTransactions">
                                {{--                        table for all Transactions--}}
                                <div class="table-responsive">
                                    <table id="allTransactionsTable"
                                           class="table table-borderless table-hover table-striped">
                                        <thead class="table-info">
                                        <tr>
                                            <th></th>
                                            <th>File</th>
                                            <th>Size</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($paginated as $file)
                                            @if($file->isFile())
                                                <tr>
                                                    <td>#</td>
                                                    <td>
                                                        {{$file->getFilename()}}
                                                    </td>
                                                    @if( 1 <= $size = number_format($file->getSize() / (1024*1024*1024), 2) )
                                                        <td>{{$size}}GB</td>
                                                    @elseif( 1 <= $size = number_format($file->getSize() / (1024*1024), 2)  )
                                                        <td>{{$size}} MB</td>
                                                    @elseif( 1 <= $size = number_format($file->getSize() / (1024), 2)  )
                                                        <td>{{$size}} kB</td>
                                                    @else
                                                        <td>0 KB</td>
                                                    @endif

                                                    <td class="text-right">
                                                        @if(str_contains($file->getFilename(),'laravel'))
                                                            <a href="/log-viewer/logs/{{$file->getFilename()}}" class="btn btn-sm btn-info">
                                                                <i class="fa fa-search"></i>
                                                            </a>
                                                        @endif
                                                        <a href="/download/{{$file->getFilename()}}/{{$directory}}"  target="_new" id="data" class="btn btn-sm btn-success">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                        <a href="/delete/{{$file->getFilename()}}/{{$directory}}" class="btn btn-sm btn-danger" >
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>

                                                </tr>
                                            @endif
                                        @endforeach
                                        <tr>
                                            @if($paginationCount > 1)
                                                <td colspan="100%">
                                                    <ul class="pagination">

                                                        <li class="page-item disabled" aria-disabled="true"
                                                            aria-label="« Previous">
                                                            <span class="page-link" aria-hidden="true">‹</span>
                                                        </li>
                                                        @for($i = 1; $i <= $paginationCount; $i++)

                                                            <li class="page-item @if(request()->query("page") == $i) active"
                                                                aria-current="page" @else " @endif ><a class="page-link"
                                                                                                       href="?&page={{$i}}">{{$i}}</a></li>

                                                        @endfor


                                                    </ul>
                                                </td>
                                            @endif
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
