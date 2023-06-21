export class DeliveryBoy {
    static select_helper() {
        return {
            option: {
                value: 'id', label: function (delivery_boy) {
                    return delivery_boy.first_name + " " + delivery_boy.last_name + " [" + delivery_boy.mobile_number + "]";
                }
            }
        };
    }
}
