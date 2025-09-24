import TicketController from './TicketController'
import TicketScanController from './TicketScanController'
import Settings from './Settings'
import Auth from './Auth'
import EventController from './EventController'
import WaitlistController from './WaitlistController'
const Controllers = {
    TicketController: Object.assign(TicketController, TicketController),
TicketScanController: Object.assign(TicketScanController, TicketScanController),
Settings: Object.assign(Settings, Settings),
Auth: Object.assign(Auth, Auth),
EventController: Object.assign(EventController, EventController),
WaitlistController: Object.assign(WaitlistController, WaitlistController),
}

export default Controllers