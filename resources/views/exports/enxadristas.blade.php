<table>
    <thead>
    <tr>
        <th>id</th>
        <th>rating_id</th>
        <th>name</th>
        <th>born</th>
        <th>cidade_id</th>
        <th>clube_id</th>
        <th>created_at</th>
        <th>updated_at</th>
        <th>cbx_id</th>
        <th>fide_id</th>
        <th>email</th>
        <th>sexos_id</th>
        <th>celular</th>
        <th>lbx_id</th>
        <th>fide_last_update</th>
        <th>cbx_last_update</th>
        <th>lbx_last_update</th>
    </tr>
    </thead>
    <tbody>
    @foreach($enxadristas as $enxadrista)
        <tr>
            <td>{{ $enxadrista->id }}</td>
            <td>{{ $enxadrista->rating_id }}</td>
            <td>{{ $enxadrista->gerNameSemCaracteresEspeciais() }}</td>
            <td>{{ $enxadrista->born }}</td>
            <td>{{ $enxadrista->cidade_id }}</td>
            <td>{{ $enxadrista->clube_id }}</td>
            <td>{{ $enxadrista->created_at }}</td>
            <td>{{ $enxadrista->updated_at }}</td>
            <td>{{ $enxadrista->cbx_id }}</td>
            <td>{{ $enxadrista->fide_id }}</td>
            <td>{{ $enxadrista->email }}</td>
            <td>{{ $enxadrista->sexos_id }}</td>
            <td>{{ $enxadrista->celular }}</td>
            <td>{{ $enxadrista->lbx_id }}</td>
            <td>{{ $enxadrista->fide_last_update }}</td>
            <td>{{ $enxadrista->cbx_last_update }}</td>
            <td>{{ $enxadrista->lbx_last_update }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
