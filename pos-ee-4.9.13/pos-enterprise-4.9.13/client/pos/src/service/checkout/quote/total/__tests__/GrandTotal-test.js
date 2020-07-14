import GrandTotalService from '../GrandTotalService';

let data = [
  {
    testCaseId: 'CGT-06',
    title: 'Display exactly the amount of Grand Total when the input values = 0',
    data: {
      subtotal: 0,
      shipping: 0,
    },
    expect: {
      grand_total: 0,
      base_grand_total: 0,
    }
  },
  {
    testCaseId: 'CGT-07',
    title: 'Display exactly the amount of Grand Total with the input values as: 1 & (-2)',
    data: {
      subtotal: 1,
      discount: -2,
    },
    expect: {
      grand_total: 0,
      base_grand_total: 0,
    }
  },
  {
    testCaseId: 'CGT-08',
    title: 'Display exactly the amount of Grand Total with the input values as: 0.1 & 0.2',
    data: {
      subtotal: 0.2,
      shipping: 0.1,
    },
    expect: {
      grand_total: 0.3,
      base_grand_total: 0.3,
    }
  },
  {
    testCaseId: 'CGT-09',
    title: 'Display exactly the amount of Grand Total with the input values as: 1 & 1000000',
    data: {
      subtotal: 1000000.0,
      shipping: 1.0,
    },
    expect: {
      grand_total: 1000001.0,
      base_grand_total: 1000001.0,
    }
  },
  {
    testCaseId: 'CGT-10',
    title: 'Display exactly the amount of Grand Total with the input values as: 1 & 2',
    data: {
      subtotal: 1,
      shipping: 2,
    },
    expect: {
      grand_total: 3,
      base_grand_total: 3,
    }
  },
  {
    testCaseId: 'CGT-11',
    title: 'Display exactly the amount of Grand Total with the input values as: 0.5 & (-0.4)',
    data: {
      subtotal: 0.5,
      discount: -0.4,
    },
    expect: {
      grand_total: 0.1,
      base_grand_total: 0.1,
    }
  }
];
for (let i = 0; i < data.length; i++) {
  it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
    let total = {
      "totalAmounts": {
        "subtotal": 0,
        "tax_subtotal": 0,
        "tax": 0,
        "discount_tax_compensation": 0,
        "shipping": 0,
        "tax_shipping": 0,
        "shipping_discount_tax_compensation": 0,
        "discount": 0,
        "rewardpoint": 0,
        "giftcard": 0,
        "weee_tax": 0,
        "giftvoucheraftertax": 0
      },
      "baseTotalAmounts": {
        "subtotal": 0,
        "tax_subtotal": 0,
        "tax": 0,
        "discount_tax_compensation": 0,
        "shipping": 0,
        "tax_shipping": 0,
        "shipping_discount_tax_compensation": 0,
        "discount": 0,
        "rewardpoint": 0,
        "giftcard": 0,
        "weee_tax": 0,
        "giftvoucheraftertax": 0
      },
    };
    total.totalAmounts = {...total.totalAmounts, ...data[i].data};
    total.baseTotalAmounts = {...total.baseTotalAmounts, ...data[i].data};

    GrandTotalService.collect({}, {}, total);

    expect(total.grand_total).toEqual(data[i].expect.grand_total);
    expect(total.base_grand_total).toEqual(data[i].expect.base_grand_total);
  });
}
