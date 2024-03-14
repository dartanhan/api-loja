$(document).ready(function() {

    $('#table').DataTable({
        "language": {
            "url": "../public/Portuguese-Brasil.json"
        },
        "order": [[ 5, "desc" ]]

    });

    $('#tableCreate').DataTable({
        "language": {
            "url": "../public/Portuguese-Brasil.json"
        },
        "order": [[ 5, "desc" ]]

    });
});