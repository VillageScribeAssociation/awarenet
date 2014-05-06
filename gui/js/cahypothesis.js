/**
 *  Constructor for hypothesis objects
 */

function CAHypothesis(cellId, value) {

    var hyp = {
        'hID': cellId + '___' + value,
        'value': value,
        'premises' = [],                    //  AND of
        'dead': false
    }
    
    return hyp;
}
