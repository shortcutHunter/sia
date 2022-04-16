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
      return dataservice.getData('paket_register_ulang', vm.page).then(function(response) {
        vm.pageData = response;
        vm.data = response.data;
      });
    }

  }
})();
