package org.easyb.bdd.money

import maris.learning.unittesting.*;

description "This story is about currency management"

narrative "this string is required for now", {
    as_a "person who uses money"
    i_want "to be able to add them together"
    so_that "that I can become rich (and wierd)"
}

scenario "two moneys of the same currency are added", {

    given "one money object is added to another", {
        nbooks = new Person().getMaxbooks();
    }

    then "the total amount should be the sum of the two", {
        nbooks.shouldBe 3
    }

}

scenario "two moneys of different currencies are added", {

}