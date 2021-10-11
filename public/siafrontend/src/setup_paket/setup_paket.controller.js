(function() {
  'use strict';

  angular
    .module('app.setup_paket')
    .controller('SetupPaketController', SetupPaketController);

  SetupPaketController.$inject = ['$q', 'dataservice', 'logger', '$scope', '$compile', '$element'];
  /* @ngInject */
  function SetupPaketController($q, dataservice, logger, scope, compile, element) {
    var vm = this;
    vm.title = 'Paket Tagihan';
    vm.page = 0;
    vm.data = [];
    vm.pageData = {};

    vm.aturPaket = aturPaket;
    vm.buatTagihan = buatTagihan;

    activate();

    scope.$watch('vm.page', function(newVal){
      if (newVal != 0) {
        getData();
      }
    });

    function activate() {
      vm.page = 1;
    }

    function getData() {
      return dataservice.getData('tahun_ajaran', vm.page).then(function(response) {
        vm.pageData = response;
        vm.data = response.data;
        logger.info('Data loaded');
      });
    }

    function aturPaket(data) {
      vm.active_data = data;
      let el = "<modal-atur-paket data='vm.active_data'></modal-atur-paket>";
      el = compile(el)(scope);
      element.append(el);
    }

    function buatTagihan(data) {
      vm.active_data = data;
      let el = "<modal-buat-tagihan data='vm.active_data'></modal-buat-tagihan>";
      el = compile(el)(scope);
    }

  }
})();
