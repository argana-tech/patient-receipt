//
//  Server.swift
//  patient_reception
//
//  Created by nishimura on 2018/09/27.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit

class Server: NSObject {
    
    // 接続先
    private static let url = "192.168.1.3"
    
    
    public static func getUrl (api:Api) -> String {
        return "http://\(url)/api/\(api.getAPI())"
    }
    
    enum Api {
        
        case check(action:Check)
        case receipt(action:Receipt)
        case notification(action:Notification)
        case locate(action:Locate)
        case settings(action: Settings)
        
        func getAPI() -> String {
            switch self {
            case .check(let action):
                return "check/\(action.rawValue)"
            case .receipt(let action):
                return "receipt/\(action.rawValue)"
            case .notification(let action):
                return "notification/\(action.rawValue)"
            case .locate(let action):
                return "locate/\(action.rawValue)"
            case .settings(let action):
                return "settings/\(action.rawValue)"
            }
        }
        
        
        // controller
        enum Check:String {
            case register = "register"
            case time = "time"
        }
        
        enum Receipt:String {
            case register = "register"
            case entry = "entry"
        }
        
        enum Notification:String {
            case read = "read"
        }
        
        enum Locate:String {
            case beacon = "add_beacon"
            case geo = "add_geo"
        }
        
        enum Settings: String {
            case property = "property"
        }
        
    }
}

