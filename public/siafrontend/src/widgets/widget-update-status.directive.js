import swal from 'sweetalert';

(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetUpdateStatus', widgetUpdateStatus);

  widgetUpdateStatus.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function widgetUpdateStatus(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link
    };
    return directive;

    function link(scope, element, attr) {
      element.on('click', function(){
        let vm = scope.$parent.vm;
        let data = {status: attr.widgetUpdateStatus};
        let id = attr.targetId ? attr.targetId : vm.data.id;
        let warning_text = `Apakah mahasiswa benar di ${attr.widgetUpdateStatus} ?`;

        if (attr.widgetUpdateStatus == 'ujian') {
          warning_text = "Apakah pembayaran mahasiswa sudah benar diterima ?";
        }

        if (attr.widgetUpdateStatus == 'aktif' || attr.widgetUpdateStatus == 'nonaktif') {
          warning_text = `Apakah yakin ingin men${attr.widgetUpdateStatus}kan dosen ini ?`;
        }

        swal({
          title: warning_text,
          icon: "warning",
          buttons: {
            cancel: true,
            text: "Iya"
          }
        })
        .then(willDelete => {
          if (willDelete) {
            dataservice.postData(vm.table, data, id).then(function(data){
              state.reload();
            });
          }
        });

        
      });
    }
  }
})();
