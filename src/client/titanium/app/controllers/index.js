var endpoint = "http://172.16.134.92/~ryousuke/APPfog/busapp/buskul/src/server/";

var selected_busstop_id = 1;
if(! Ti.App.Properties.hasProperty('selected_busstop_id')){
    // 初期化
    Ti.App.Properties.setString('selected_busstop_id', 0);
    Ti.App.Properties.setString('selected_busstop_name', "");
}

// 以前の情報を取得
selected_busstop_id = Ti.App.Properties.getString('selected_busstop_id');
selected_busstop_name = Ti.App.Properties.getString('selected_busstop_name');

// 残りバス停更新
var refreshRemaining = function(evt){
    // バス停名の変更
    selected_busstop_id = Ti.App.Properties.getString('selected_busstop_id');
    selected_busstop_name = Ti.App.Properties.getString('selected_busstop_name');
    $.waitWin.title = "BUSKUL: " + selected_busstop_name;
    
    // 残りバス停数更新 - Minami
    var xhr = Titanium.Network.createHTTPClient();
    xhr.open('GET', endpoint + 'minami.php?startpoint=' + selected_busstop_id + '&endpoint=241&time=' + parseInt(new Date), false);
    xhr.onerror = function(error){ alert(error) };
    xhr.onload = function(){
        res = JSON.parse(xhr.responseText);
        
        Ti.API.info("Retrieve: " + res.busstop.remaining);
        
        if(res.busstop.remaining == "NULL"){
            $.remainder_number.text = '?';
        }else{
            $.remainder_number.text = res.busstop.remaining;
        }
    };
    xhr.send();
};
setInterval(refreshRemaining, 100000);

// // Higashi API を叩いて、設定pickerを埋める
var xhr = Titanium.Network.createHTTPClient();
xhr.open('GET', endpoint + 'higashi.php?time=' + parseInt(new Date), false);
xhr.onload = function(){
    res = JSON.parse(xhr.responseText);
    
    for(i=0; i<res.busstop.length; i++){
      var row = Ti.UI.createPickerRow();
      row.title = res.busstop[i].name;
      row.busstop_id = res.busstop[i].id;
      row.order_id = res.busstop[i].order_id;
      $.homeBusStopPickerColumn.addRow(row);
      
      // 以前選択していたバス停を選択する
      if(row.busstop_id == selected_busstop_id){
          $.homeBusStopPicker.setSelectedRow(0, i, false);
          $.waitWin.title = "BUSKUL: " + res.busstop[i].name;
      }
    }
};
xhr.onerror = function(error){ alert(error) };
xhr.send();

// Picker
$.homeBusStopPicker.addEventListener('change', function(evt){
    Ti.API.info("Saved: selected_busstop_id=" + evt.row.busstop_id);
    Ti.App.Properties.setString('selected_busstop_id', evt.row.busstop_id);
    Ti.App.Properties.setString('selected_busstop_name', evt.row.title);
});

// WaitWin
$.waitWin.addEventListener('focus', refreshRemaining);

$.index.open();
