(function() {
  'use strict';

  angular
    .module('app.setup_paket')
    .controller('SetupPaketFormController', SetupPaketFormController);

  SetupPaketFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$element', '$scope'];
  /* @ngInject */
  function SetupPaketFormController($q, dataservice, logger, stateParams, compile, element, scope) {
    var vm = this;
    vm.title = 'Form Setup Paket';
    vm.table = 'paket_register_ulang';
    vm.data = {};

    vm.tambahPembiayaan = tambahPembiayaan;
    vm.tambahPaket      = tambahPaket;
    vm.removeData       = removeData;

    scope.$watch('vm.data.paket_register_ulang_item', itemChanges, true);

    activate();

    function activate() {
      var promises = [getOption()];
      if (stateParams.dataId) {
        promises.push(getDataDetail());
      }
      return $q.all(promises).then(function() {
        logger.info('Data loaded');
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }

    function getOption() {
      return dataservice.getOption(vm.table).then(function(response) {
        vm.option = response;
      });
    }

    function tambahPembiayaan() {
      let el = "<modal-pembiayaan-tagihan ng-model='vm.data.paket_register_ulang_item'></modal-pembiayaan-tagihan>";
      el = compile(el)(scope);
      $(element).append(el);
    }

    function tambahPaket() {
      let el = "<modal-tambah-paket ng-model='vm.data.paket_register_ulang_item'></modal-tambah-paket>";
      el = compile(el)(scope);
      $(element).append(el);
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

          vm.data.nominal = total_nominal;
          vm.data.paket_register_ulang_item = convertedVal;
        }
      }
    }

    function removeData(indx) {
      vm.data.paket_register_ulang_item.splice(indx, 1);
    }
  }
})();
