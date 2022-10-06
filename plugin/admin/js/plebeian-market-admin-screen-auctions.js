$(document).ready( function () {
    $('#table_auctions_listing').DataTable({
        order: [[4, 'desc']],
        dom: '<"toolbar">Bfrtip',
        buttons: [
            'colvis',
            'csv',
            {
                text: 'New auction',
                className: 'createButton',
                attr: {
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#add-auction-modal'
                }
            }
        ],
        select: true,
        columnDefs: [
            {
                targets: [4, 5, 6],
                render: DataTable.render.datetime(),
            },
            {
                targets: 1,
                render: DataTable.render.text(),
            },
        ],
    });
} );