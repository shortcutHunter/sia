(function() {
  'use strict';

  angular
    .module('app.import')
    .controller('ImportController', ImportController);

  ImportController.$inject = ['$q', 'dataservice', 'logger', '$scope'];
  /* @ngInject */
  function ImportController($q, dataservice, logger, scope) {
    var vm = this;
    vm.title = 'Import';
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
      return dataservice.getUrl('/rekap/semester').then(function(response) {
        vm.pageData = response;
        vm.data = response.data;
      });
    }

  }
})();
