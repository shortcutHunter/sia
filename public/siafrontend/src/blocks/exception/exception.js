(function() {
  'use strict';

  angular
    .module('blocks.exception')
    .factory('exception', exception);

  /* @ngInject */
  exception.$inject = ['$q', 'logger'];

  function exception($q, logger) {
    var service = {
      catcher: catcher
    };
    return service;

    function catcher(message) {
      return function(e) {
        var newException;
        if (e.data && e.data.exception.length > 0) {
          newException = e.data.exception[0];
          logger.error(newException.message, e);
          return $q.reject(newException.message);
        }

        return $q.reject(e);
      };
    }
  }
})();
