@extends('back.layout')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
    <style>
        a > * { pointer-events: none; }
    </style>
@endsection

@section('main')
    <table id="postsTable" class="table table-bordered">
        <thead>
        <tr>
            <th>Title</th>
            <th>Updated At</th>
            <th>Scheduled At</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($posts as $post)
            <tr>
                <td>{{$post->title}}</td>
                <td>{{$post->updated_at}}</td>
                <td>{{ $post->scheduled_at ?? 'Not Scheduled' }}</td>
                <td>
                    @if($post->status == 'archived')
                        Archivé
                    @elseif($post->scheduled_at && $post->scheduled_at > now())
                        Programmeé
                    @elseif(!$post->scheduled_at)
                        Brouillon
                    @else
                        Publié
                    @endif
                </td>
                <td>
                    <a class="btn btn-primary" href="">Edit</a>
                    @if($post->status == 'archived')
                        <a class="btn btn-secondary" href="">unarchived</a>
                    @else
                        <a class="btn btn-warning" href="">archived</a>
                    @endif
                    <a class="btn btn-danger" href="">Edit</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#postsTable').DataTable();
        });
    </script>
@endsection