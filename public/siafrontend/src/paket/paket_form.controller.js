(function() {
  'use strict';

  angular
    .module('app.paket')
    .controller('PaketFormController', PaketFormController);

  PaketFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$scope'];
  /* @ngInject */
  function PaketFormController($q, dataservice, logger, stateParams, compile, scope) {
    var vm = this;
    vm.title = 'Form Paket';
    vm.table = 'paket';
    vm.data = {};

    vm.tambahPembiayaan = tambahPembiayaan;

    activate();

    scope.$watch('vm.data.item', itemChanges, true);

    function activate() {
      if (stateParams.dataId) {
        var promises = [getDataDetail()];
        return $q.all(promises).then(function() {
          
        });
      }
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }

    function tambahPembiayaan() {
      let el = "<modal-tambah-pembiayaan ng-model='vm.data.item'></modal-tambah-pembiayaan>";
      el = compile(el)(scope);
    }

    function itemChanges(newVal, oldVal) {
      let total_nominal = 0;
      if (newVal) {
        $.each(newVal, function(i, v){
          total_nominal += parseInt(v.nominal);
        });
      }
      vm.data.nominal = total_nominal;
    }
  }
})();
