import Settings from './Settings'
import DashboardController from './DashboardController'
import CustomerController from './CustomerController'
import OrderController from './OrderController'
import MeasurementController from './MeasurementController'

const Controllers = {
    Settings: Object.assign(Settings, Settings),
    DashboardController: Object.assign(DashboardController, DashboardController),
    CustomerController: Object.assign(CustomerController, CustomerController),
    OrderController: Object.assign(OrderController, OrderController),
    MeasurementController: Object.assign(MeasurementController, MeasurementController),
}

export default Controllers