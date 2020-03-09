
var businessDocViewAutocompleteColumns = [];
var businessDocViewLineData = [];
var businessDocViewFormName = "f_document_primary";
var businessDocViewUrl = "";
var hsTable = null;

function beforeChange(changes, source) {
    // Check if the value has changed. Not Multiselection
    if (changes !== null && changes[0][2] !== changes[0][3]) {
        for (var i = 0; i < businessDocViewAutocompleteColumns.length; i++) {
            if (changes[0][1] === businessDocViewAutocompleteColumns[i]) {
                // apply for autocomplete columns
                if (typeof changes[0][3] === "string") {
                    changes[0][3] = changes[0][3].split(" | ", 1)[0];
                    var position = hsTable.getSelected();
                    hsTable.setDataAtCell(position[0][0], 2, '');
                }
            }
        }
    }
}

function businessDocViewAutocompleteGetData(formId, formData, term) {
    var rawForm = $("form[id=" + formId + "]").serializeArray();
    $.each(rawForm, function (i, input) {
        formData[input.name] = input.value;
    });
    formData["action"] = "autocomplete";
    formData["term"] = term;
    return formData;
}

function businessDocViewSubjectChanged() {
    var data = {};
    $.each($("#" + businessDocViewFormName).serializeArray(), function (key, value) {
        data[value.name] = value.value;
    });
    data.action = "subject-changed";
    console.log("data", data);

    $.ajax({
        type: "POST",
        url: businessDocViewUrl,
        dataType: "json",
        data: data,
        success: function (results) {
            $("#doc_codpago").val(results.codpago);
            $("#doc_codserie").val(results.codserie);
            console.log("results", results);

            businessDocViewRecalculate();
        },
        error: function (xhr, status, error) {
            alert(xhr.responseText);
        }
    });
}
async function productHasTraceability(row){
    let data_row =  hsTable.getDataAtRow(row);
    if (data_row[1] !== null){
        let data = {};
        data.action = 'get-trazabilidad';
        data.product = {
            referencia: data_row[0],
            descripcion: data_row[1]
        };
        const response = await $.ajax({
            type: "POST",
            url: businessDocViewUrl,
            dataType: "json",
            data: data,
            success: async function (results) {
                console.log('trazabilidad', results);
            },
            error: function (xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
        return response;
    }
    return  null;
}
async function businessDocViewRecalculate(change = null, source = null) {
    var data = {};
    $.each($("#" + businessDocViewFormName).serializeArray(), function (key, value) {
        data[value.name] = value.value;
    });
    data.action = "recalculate-document";
    data.lines = getGridData();
    console.log("data", data);
    if (change !== null && change[0][2] !== change[0][3] && change[0][1] == 'cantidad') {
        let hasTrazabilidad = await productHasTraceability(change[0][0]);
        if (hasTrazabilidad !== null && hasTrazabilidad.trazabilidad == 'series')
        {
            var newLines = [], k = 0;
            for(let i = 0; i < data.lines.length; i++, k++) {
                if (i < change[0][0]) {
                    newLines[k] = data.lines[i];
                } else {
                    let num_rows = data.lines[i].cantidad;
                    for(let j = 0; j < num_rows; j++, k++) {
                        newLines[k] = data.lines[i];
                        newLines[k].cantidad = 1;
                    }
                }
            }
            data.lines = newLines;
        }
    }
    $.ajax({
        type: "POST",
        url: businessDocViewUrl,
        dataType: "json",
        data: data,
        success: function (results) {
            $("#doc_neto").val(results.doc.neto);
            $("#doc_netosindto").val(results.doc.netosindto);
            $("#doc_total").val(results.doc.total);
            $("#doc_total2").val(results.doc.total);
            $("#doc_totaliva").val(results.doc.totaliva);
            $("#doc_totalrecargo").val(results.doc.totalrecargo);
            $("#doc_totalirpf").val(results.doc.totalirpf);

            var rowPos = 0;
            results.lines.forEach(function (element) {
                var visualRow = hsTable.toVisualRow(rowPos);
                businessDocViewLineData.rows[visualRow] = element;
                rowPos++;
            });

            hsTable.render();
            console.log("results", results);
        },
        error: function (xhr, status, error) {
            alert(xhr.responseText);
        }
    });
}

function businessDocViewSave() {
    $("#btn-document-save").prop("disabled", true);

    var data = {};
    $.each($("#" + businessDocViewFormName).serializeArray(), function (key, value) {
        data[value.name] = value.value;
    });
    data.action = "save-document";
    data.lines = getGridData();
    console.log(data);

    $.ajax({
        type: "POST",
        url: businessDocViewUrl,
        dataType: "text",
        data: data,
        success: function (results) {
            if (results.substring(0, 3) === "OK:") {
                $("#" + businessDocViewFormName + " :input[name=\"action\"]").val('save-ok');
                $("#" + businessDocViewFormName).attr('action', results.substring(3)).submit();
            } else {
                alert(results);
                $("#" + businessDocViewFormName + " :input[name=\"multireqtoken\"]").val(randomString(20));
            }
        },
        error: function (msg) {
            alert(msg.status + " " + msg.responseText);
        }
    });

    $("#btn-document-save").prop("disabled", false);
}

function businessDocViewSetAutocompletes(columns) {
    for (var key = 0; key < columns.length; key++) {
        if (columns[key].type === "autocomplete") {
            businessDocViewAutocompleteColumns.push(columns[key].data);
            var source = columns[key].source["source"];
            var field = columns[key].source["fieldcode"];
            var title = columns[key].source["fieldtitle"];
            columns[key].source = function (query, process) {
                var ajaxData = {
                    term: query,
                    action: "autocomplete",
                    field: field,
                    source: source,
                    fieldcode: field,
                    fieldtitle: title
                };
                $.ajax({
                    type: "POST",
                    url: businessDocViewUrl,
                    dataType: "json",
                    data: ajaxData,
                    success: function (response) {
                        var values = [];
                        response.forEach(function (element) {
                            values.push(element.key + " | " + element.value);
                        });
                        process(values);
                    },
                    error: function (msg) {
                        alert(msg.status + " " + msg.responseText);
                    }
                });
            };
        }
    }

    return columns;
}

function getGridData() {
    var rowIndex, lines = [];
    for (var i = 0, max = businessDocViewLineData.rows.length; i < max; i++) {
        rowIndex = hsTable.toVisualRow(i);
        if (hsTable.isEmptyRow(rowIndex)) {
            continue;
        }

        lines[rowIndex] = businessDocViewLineData.rows[i];
    }
    return lines;
}

function randomString(length) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

$(document).ready(function () {
    var container = document.getElementById("document-lines");
    hsTable = new Handsontable(container, {
        data: businessDocViewLineData.rows,
        columns: businessDocViewSetAutocompletes(businessDocViewLineData.columns),
        rowHeaders: true,
        colHeaders: businessDocViewLineData.headers,
        stretchH: "all",
        autoWrapRow: true,
        manualRowResize: true,
        manualColumnResize: true,
        manualRowMove: true,
        manualColumnMove: false,
        contextMenu: true,
        filters: true,
        dropdownMenu: true,
        preventOverflow: "horizontal",
        minSpareRows: 5,
        enterMoves: {row: 0, col: 1},
        modifyColWidth: function (width, col) {
            if (width > 500) {
                return 500;
            }
        }
    });

    Handsontable.hooks.add("beforeChange", beforeChange);
    Handsontable.hooks.add("afterChange", businessDocViewRecalculate);

    $("#mainTabs li:first-child a").on('shown.bs.tab', function (e) {
        hsTable.render();
    });

    $("#doc_codserie, #doc_dtopor1, #doc_dtopor2").change(function () {
        businessDocViewRecalculate();
    });

    $(".autocomplete-dc").each(function () {
        var data = {
            field: $(this).attr("data-field"),
            fieldcode: $(this).attr("data-fieldcode"),
            fieldtitle: $(this).attr("data-fieldtitle"),
            source: $(this).attr("data-source")
        };
        var formName = $(this).closest("form").attr("name");
        $(this).autocomplete({
            source: function (request, response) {
                $.ajax({
                    method: "POST",
                    url: businessDocViewUrl,
                    data: businessDocViewAutocompleteGetData(formName, data, request.term),
                    dataType: "json",
                    success: function (results) {
                        var values = [];
                        results.forEach(function (element) {
                            if (element.key === null || element.key === element.value) {
                                values.push(element);
                            } else {
                                values.push({key: element.key, value: element.key + " | " + element.value});
                            }
                        });
                        response(values);
                    },
                    error: function (msg) {
                        alert(msg.status + " " + msg.responseText);
                    }
                });
            },
            select: function (event, ui) {
                if (ui.item.key !== null) {
                    $("#" + data.field + "Autocomplete").val(ui.item.key);
                    var value = ui.item.value.split(" | ");
                    if (value.length > 1) {
                        ui.item.value = value[1];
                    } else {
                        ui.item.value = value[0];
                    }
                    businessDocViewSubjectChanged();
                }
            }
        });
    });
});