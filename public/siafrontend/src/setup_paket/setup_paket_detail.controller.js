(function() {
  'use strict';

  angular
    .module('app.setup_paket')
    .controller('SetupPaketDetailController', SetupPaketDetailController);

  SetupPaketDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$scope'];
  /* @ngInject */
  function SetupPaketDetailController($q, dataservice, logger, stateParams, compile, scope) {
    var vm = this;
    vm.title = 'Detail Setup Paket';
    vm.table = 'paket_register_ulang';
    vm.data = {};

    vm.buatTagihan = buatTagihan;

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }

    function buatTagihan(data) {
      let el = "<modal-buat-tagihan data='vm.data'></modal-buat-tagihan>";
      el = compile(el)(scope);
    }
  }
})();
