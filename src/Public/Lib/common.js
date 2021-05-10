/**
 * Created by Neo on 2017/3/14.
 */
var status = {
    url:'',
    data:{},
    map:{id:'id'},
    init:function (url,data) {
        this.url=url;
        this.data=data;
    },
    del: function (id) {
        this.send(id, 'del')
    },
    //启用
    start: function (id) {
        this.send(id, 'start')
    },
    //停用
    stop: function (id) {
        this.send(id, 'stop')
    },
    //执行
    send: function (id, status) {
        var url=this.url;
        var data=this.data;
        $.ajax({
            type: 'GET',
            url: url,
            data: data,
            dataType: 'json',
            success: function (result) {
                if (result.status == 1) {
                    layer.alert(result.info, {icon: 6}, function () {
                        location.reload();
                    });
                } else {
                    layer.alert(result.info, {icon: 5});
                }
            }

        })
    }

};
