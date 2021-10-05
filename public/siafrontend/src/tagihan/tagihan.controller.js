(function() {
  'use strict';

  angular
    .module('app.tagihan')
    .controller('TagihanController', TagihanController);

  TagihanController.$inject = ['$q', 'dataservice', 'logger', '$scope'];
  /* @ngInject */
  function TagihanController($q, dataservice, logger, scope) {
    var vm = this;
    vm.title = 'Tagihan';
    vm.table = 'tagihan';
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
      return dataservice.getData(vm.table, vm.page).then(function(response) {
        vm.pageData = response;
        vm.data = response.data;
        logger.info('Data loaded');
      });
    }

  }
})();
