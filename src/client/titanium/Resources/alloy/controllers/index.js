function Controller() {
    require("alloy/controllers/BaseController").apply(this, Array.prototype.slice.call(arguments));
    $model = arguments[0] ? arguments[0].$model : null;
    var $ = this, exports = {}, __defers = {};
    $.__views.index = Ti.UI.createTabGroup({
        backgroundColor: "white",
        activeTab: "waitTab",
        id: "index"
    });
    $.__views.waitWin = Ti.UI.createWindow({
        layout: "vertical",
        backgroundImage: "icons/background_64.png",
        backgroundRepeat: "true",
        id: "waitWin",
        title: "BUSKUL: 巣子"
    });
    $.__views.remainder_before = Ti.UI.createLabel({
        color: "black",
        font: {
            fontSize: "28dp"
        },
        width: "100%",
        height: "10%",
        textAlign: Ti.UI.TEXT_ALIGNMENT_LEFT,
        text: "次のバスは",
        id: "remainder_before"
    });
    $.__views.waitWin.add($.__views.remainder_before);
    $.__views.remainderView = Ti.UI.createView({
        width: "100%",
        height: "80%",
        id: "remainderView"
    });
    $.__views.waitWin.add($.__views.remainderView);
    $.__views.remainder_number = Ti.UI.createLabel({
        color: "black",
        font: {
            fontSize: "224dp"
        },
        textAlign: Ti.UI.TEXT_ALIGNMENT_CENTER,
        id: "remainder_number"
    });
    $.__views.remainderView.add($.__views.remainder_number);
    $.__views.remainder_unit = Ti.UI.createLabel({
        color: "black",
        font: {
            fontSize: "28dp"
        },
        width: "100%",
        textAlign: Ti.UI.TEXT_ALIGNMENT_RIGHT,
        bottom: "20%",
        right: "10%",
        text: "つ前",
        id: "remainder_unit"
    });
    $.__views.remainderView.add($.__views.remainder_unit);
    $.__views.remainder_after = Ti.UI.createLabel({
        color: "black",
        font: {
            fontSize: "28dp"
        },
        width: "100%",
        height: "10%",
        textAlign: Ti.UI.TEXT_ALIGNMENT_RIGHT,
        text: "のバス停にいます",
        id: "remainder_after"
    });
    $.__views.waitWin.add($.__views.remainder_after);
    $.__views.waitTab = Ti.UI.createTab({
        window: $.__views.waitWin,
        id: "waitTab",
        title: "BUSKUL",
        icon: "icons/bus_icon.png"
    });
    $.__views.index.addTab($.__views.waitTab);
    $.__views.settingWin = Ti.UI.createWindow({
        layout: "vertical",
        backgroundImage: "icons/background_64.png",
        backgroundRepeat: "true",
        id: "settingWin",
        title: "設定"
    });
    $.__views.__alloyId1 = Ti.UI.createView({
        height: "15%",
        width: "100%",
        id: "__alloyId1"
    });
    $.__views.settingWin.add($.__views.__alloyId1);
    $.__views.homeBusStop_lbl = Ti.UI.createLabel({
        color: "black",
        font: {
            fontSize: "20dp"
        },
        text: "最寄りのバス停を選んでください",
        id: "homeBusStop_lbl"
    });
    $.__views.__alloyId1.add($.__views.homeBusStop_lbl);
    $.__views.homeBusStopPicker = Ti.UI.createPicker({
        width: "100%",
        id: "homeBusStopPicker",
        selectionIndicator: "true",
        useSpinner: "true"
    });
    $.__views.settingWin.add($.__views.homeBusStopPicker);
    $.__views.homeBusStopPickerColumn = Ti.UI.createPickerColumn({
        id: "homeBusStopPickerColumn"
    });
    $.__views.homeBusStopPicker.add($.__views.homeBusStopPickerColumn);
    $.__views.settingTab = Ti.UI.createTab({
        window: $.__views.settingWin,
        id: "settingTab",
        title: "設定",
        icon: "icons/home_icon.png"
    });
    $.__views.index.addTab($.__views.settingTab);
    $.addTopLevelView($.__views.index);
    exports.destroy = function() {};
    _.extend($, $.__views);
    var endpoint = "http://172.16.134.92/~ryousuke/APPfog/busapp/buskul/src/server/", selected_busstop_id = 1;
    if (!Ti.App.Properties.hasProperty("selected_busstop_id")) {
        Ti.App.Properties.setString("selected_busstop_id", 0);
        Ti.App.Properties.setString("selected_busstop_name", "");
    }
    selected_busstop_id = Ti.App.Properties.getString("selected_busstop_id");
    selected_busstop_name = Ti.App.Properties.getString("selected_busstop_name");
    var refreshRemaining = function(evt) {
        selected_busstop_id = Ti.App.Properties.getString("selected_busstop_id");
        selected_busstop_name = Ti.App.Properties.getString("selected_busstop_name");
        $.waitWin.title = "BUSKUL: " + selected_busstop_name;
        var xhr = Titanium.Network.createHTTPClient();
        xhr.open("GET", endpoint + "minami.php?startpoint=" + selected_busstop_id + "&endpoint=241&time=" + parseInt(new Date), !1);
        xhr.onerror = function(error) {
            alert(error);
        };
        xhr.onload = function() {
            res = JSON.parse(xhr.responseText);
            Ti.API.info("Retrieve: " + res.busstop.remaining);
            res.busstop.remaining == "NULL" ? $.remainder_number.text = "?" : $.remainder_number.text = res.busstop.remaining;
        };
        xhr.send();
    };
    setInterval(refreshRemaining, 100000);
    var xhr = Titanium.Network.createHTTPClient();
    xhr.open("GET", endpoint + "higashi.php?time=" + parseInt(new Date), !1);
    xhr.onload = function() {
        res = JSON.parse(xhr.responseText);
        for (i = 0; i < res.busstop.length; i++) {
            var row = Ti.UI.createPickerRow();
            row.title = res.busstop[i].name;
            row.busstop_id = res.busstop[i].id;
            row.order_id = res.busstop[i].order_id;
            $.homeBusStopPickerColumn.addRow(row);
            if (row.busstop_id == selected_busstop_id) {
                $.homeBusStopPicker.setSelectedRow(0, i, !1);
                $.waitWin.title = "BUSKUL: " + res.busstop[i].name;
            }
        }
    };
    xhr.onerror = function(error) {
        alert(error);
    };
    xhr.send();
    $.homeBusStopPicker.addEventListener("change", function(evt) {
        Ti.API.info("Saved: selected_busstop_id=" + evt.row.busstop_id);
        Ti.App.Properties.setString("selected_busstop_id", evt.row.busstop_id);
        Ti.App.Properties.setString("selected_busstop_name", evt.row.title);
    });
    $.waitWin.addEventListener("focus", refreshRemaining);
    $.index.open();
    _.extend($, exports);
}

var Alloy = require("alloy"), Backbone = Alloy.Backbone, _ = Alloy._, $model;

module.exports = Controller;