<!DOCTYPE HTML>
<html>
<head>
<script>


window.onload = function (){

}


</script>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="アービトラージ">
  <title>アビトラ Ticker</title>
  <meta name="keywords" content="アービトラージ" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/tabulator/3.3.2/css/tabulator.min.css" rel="stylesheet">
  
  
</head>
<body>
        
    <h2>Welcome</h2>      
    
    <p>このサイトはアービトラージのために、複数の取引所間のUSD/BTC価格を比較できるサイトです</p>
    <p>差額のチャートを追加予定です.</p>
    <p>データの元は公開APIを利用しています.特にAPIキー等はいりません</p>
    <p> API : <a href="https://api.bitfinex.com/v1/pubticker/btcusd" target="_brank">https://api.bitfinex.com/v1/pubticker/btcusd</a></p>
    <p> API : <a href="https://poloniex.com/public?command=returnTicker" target="_brank">https://poloniex.com/public?command=returnTicker</a></p>
    <p> 美しい表は<a href="http://tabulator.info/docs/3.3" target="_brank">Tabulator</a> を利用しています.</p>
    
    
    <style>
    #flash_text{
    background-color: #b6d5ff;
    }
    </style>
    <p id="flash_text"></p>
    
    <h2>Statistics</h2>
    
    <h3>MAIN</h3>
    <div id="table-main"></div>
    
    <h3>BITFINEX</h3>
    <div id="table-sub1"></div>
    
    <h3>POLONIEX</h3>
    <div id="table-sub2"></div>
    
    <p>表は最大15行まで表示されます</p>
    
    <!-- SCRIPT -->

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tabulator/3.3.2/js/tabulator.min.js"></script>
    
    <!-- USER SCRIPT -->
    
    <script>
        
        var config = {
            // height:205, // set height of table, this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
            layout:"fitColumns", //fit columns to width of table (optional)
            pagination:"local", //enable local pagination.
            columns:[ //Define Table Columns
                {title:"Name", field:"name", width:150},
                {title:"Last", field:"last", align:"left"},
                {title:"Low", field:"low"},
                {title:"High", field:"high", align:"center"},
                {title:"Time", field:"time", align:"center"},
            ],
            rowAdded:function(e, row){
                //e - the tap event object
                //row - row component
                console.log(e.row.parent.activeRows);
                if(e.row.parent.activeRowsCount> 15){
                //     // var row
                    var rows = e.row.parent.activeRows;
                    rows.forEach(function(row){
                        row.delete();
                    });
                }
        
                
            },
        };
    
        $("#table-main").tabulator(config);
        $("#table-sub1").tabulator(config);
        $("#table-sub2").tabulator(config);


        // var tableData = [
        //     {id:1, name:"Billy Bob", age:"12", gender:"male", height:1, col:"red", dob:"", cheese:1},
        //     {id:2, name:"Mary May", age:"1", gender:"female", height:2, col:"blue", dob:"14/05/1982", cheese:true},
        // ]
        
        // $("#example-table").tabulator("setData", tableData);

        // var tableData = [
        //     {id:2, name:"Mary May", age:"120", gender:"female", height:2, col:"blue", dob:"14/05/1982", cheese:true},
        // ]
        
        // $("#example-table").tabulator("updateData", tableData);
        // $("#example-table").tabulator("updateOrAddData", tableData);

        // var tableData = [
        //     {id:2, name:"Mary May", age:"120", gender:"female", height:2, col:"blue", dob:"14/05/1982", cheese:true},
        // ]
        
        // $("#example-table").tabulator("addData", tableData);
        
        /**
         * 日付をフォーマットする
         * @param  {Date}   date     日付
         * @param  {String} [format] フォーマット
         * @return {String}          フォーマット済み日付
         */
        function formatDate(date, format) {
          if (!format) format = 'YYYY-MM-DD hh:mm:ss.SSS';
          format = format.replace(/YYYY/g, date.getFullYear());
          format = format.replace(/MM/g, ('0' + (date.getMonth() + 1)).slice(-2));
          format = format.replace(/DD/g, ('0' + date.getDate()).slice(-2));
          format = format.replace(/hh/g, ('0' + date.getHours()).slice(-2));
          format = format.replace(/mm/g, ('0' + date.getMinutes()).slice(-2));
          format = format.replace(/ss/g, ('0' + date.getSeconds()).slice(-2));
          if (format.match(/S/g)) {
            var milliSeconds = ('00' + date.getMilliseconds()).slice(-3);
            var length = format.match(/S/g).length;
            for (var i = 0; i < length; i++) format = format.replace(/S/, milliSeconds.substring(i, i + 1));
          }
          return format;
        };
        
        function load_bitfinex(){
            /* BITREX */        
            url = "https://api.bitfinex.com/v1/pubticker/btcusd";
            $.get("get_wrapper.php", {url:url}, function(data){
                gdata = JSON.parse(data);
                var d = {
                    id:1,
                    name: "bitfinex [USD/BTC]",
                    last: parseFloat(gdata["last_price"]),
                    low: parseFloat(gdata["low"]),
                    high: parseFloat(gdata["high"]),
                    time: formatDate(new Date())
                };
                $("#table-main").tabulator("updateOrAddRow", 1, d);
                $("#table-sub1").tabulator("addData", d);
                console.log(d);
            });
        };

        function load_poloniex(){
            /* POLONIEX */
            // {"BTC_LTC":{"last":"0.0251","lowestAsk":"0.02589999","highestBid":"0.0251","percentChange":"0.02390438","baseVolume":"6.16485315","quoteVolume":"245.82513926"}
            var url = "https://poloniex.com/public?command=returnTicker";
            $.get("get_wrapper.php", {url:url}, function(data){
                gdata = JSON.parse(data);
                var d = {
                    id:2,
                    name: "poloniex [USD/BTC]",
                    last: parseFloat(gdata["USDT_BTC"]["last"]),
                    low: parseFloat(gdata["USDT_BTC"]["lowestAsk"]),
                    high: parseFloat(gdata["USDT_BTC"]["highestBid"]),
                    time: formatDate(new Date())
                };
                $("#table-main").tabulator("updateOrAddRow", 2, d);
                $("#table-sub2").tabulator("addData", d);
                console.log(d);
            });
        };
        
        /* TIMER */ 
        var timer1;
        var INTERVAL=5000;
        function startTimer(){
            timer1 = setInterval(function(){
                load_bitfinex();
                load_poloniex();
            }, INTERVAL);
            flush("Timer Start..");
        };
        
        function stopTimer(){
            clearInterval(timer1);
            flush("Timer Stop.. ");
        }
        
        function flush(msg){
            var f = document.getElementById("flash_text"); 
            f.innerHTML=msg;
            
            setTimeout(function(){
                f.innerHTML="";
            }, INTERVAL);
        };
        
        /* Initialize */
        startTimer();
        
        
        
    </script>


    <input type="button" value="startTimer" onclick="startTimer();"/>
    <input type="button" value="stopTimer" onclick="stopTimer();"/>
    
<h2>ソースコード</h2>
<script src="https://gist.github.com/peace098beat/6305b4332ceee765bcc9673ba291e500.js"></script>
</body>
</html>