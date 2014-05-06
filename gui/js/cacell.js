/**
 *  Abstract cell, inheritebd by all types
 *
 *  @param  id      {String}    uniquely identifies this cell
 *  @param  species {String}    solution|mul|add|total|carry
 *  @param  x       {Number}    location in abstract co-ordinates for viewport
 *  @param  y       {Number}    location in abstract co-ordinates for viewport
 *  @param  parent1 {String}    cellId of first parent, if any
 *  @param  parent2 {String}    cellId of second parent, if any
 */

function CACell(id, species, x, y, parent1, parent2) {

    var basicCell = {
        'id': 
        'species': species,
        'parent1': parent1,
        'parent2': parent2,
        'max': -1,
        'min': -1,
        'hypotheses': []            //  set of hypotheses valid for this object
        'blocked': []               //  set of hypotheses disproven
        'x': x,
        'y': y
    };

    return basicCell;
}
