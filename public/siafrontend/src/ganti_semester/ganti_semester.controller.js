(function() {
  'use strict';

  angular
    .module('app.ganti_semester')
    .controller('GantiSemesterController', GantiSemesterController);

  GantiSemesterController.$inject = ['$q', 'dataservice', 'logger', '$scope'];
  /* @ngInject */
  function GantiSemesterController($q, dataservice, logger, scope) {
    var vm = this;
    vm.title = 'Ganti Semester';
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
        logger.info('Data loaded');
      });
    }

  }
})();
