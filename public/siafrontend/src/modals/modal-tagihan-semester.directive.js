(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalTagihanSemester', modalTagihanSemester);

  modalTagihanSemester.$inject = ['$state', 'dataservice', '$compile'];

  /* @ngInject */
  function modalTagihanSemester(state, dataservice, compile) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/tagihan_semester',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      scope.closeModal = closeModal;

      scope.removeData = removeData;
      scope.tambahPembiayaan = tambahPembiayaan;
      scope.gantiSemester = gantiSemester;

      scope.$watch('setup_paket.paket_register_ulang_item', itemChanges, true);

      activate();

      function activate() {
        $(element).modal('show');

        getData();

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function tambahPembiayaan() {
        let el = "<modal-pembiayaan-tagihan ng-model='setup_paket.paket_register_ulang_item'></modal-pembiayaan-tagihan>";
        el = compile(el)(scope);
        $(element).parent().append(el);
      }

      function getData() {
        let filter = `semester_id=${scope.data.semester_ganti_id}`;
        dataservice.getDataFilter('paket_register_ulang', filter).then(function(response){
          scope.setup_paket = response.data[0];
        });
      }

      function closeModal() {
        $(element).modal('hide');
      }

      function removeData(indx) {
        scope.setup_paket.paket_register_ulang_item.splice(indx, 1);
      }

      function itemChanges(newVal, oldVal) {
        let total_nominal = 0;
        let convertedVal = [];
        
        if (newVal) {
          if (!oldVal || newVal.length !== oldVal.length) {
            $.each(newVal, function(i, v){
              let data = v;
              if (!v.item) {
                v = {};
                v.item = data;
                v.item_id = data.id;
                convertedVal.push(v);
              }else{
                convertedVal.push(v);
              }
              total_nominal += parseInt(v.item.nominal);
            });

            scope.setup_paket.nominal = total_nominal;
            scope.setup_paket.paket_register_ulang_item = convertedVal;
          }
        }
      }

      function gantiSemester() {
        let data = scope.data;
        data['tagihan_item'] = scope.setup_paket.paket_register_ulang_item;
        let url = `/rekap/semester/mahasiswa`;
        return dataservice.postDataUrl(url, data).then(function(response){
          $(element).modal('hide');
          state.reload();
        });
      }

    }
  }
})();
