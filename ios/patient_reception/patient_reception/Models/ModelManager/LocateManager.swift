//
//  LocateManager.swift
//  patient_reception
//
//  Created by nishimura on 2018/09/27.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit
import Alamofire
import Alamofire_SwiftyJSON
import SwiftyJSON

class LocateManager: AbstractManager<LocateObject> {
    // ビーコン
    func postBeacon (locate:LocateObject, complitionHandler:@escaping (LocateObject) -> Void) {
        var params: [String:AnyObject] = [
            AbstractObject.Request.unigueId.rawValue : locate.uniqueId as AnyObject,
        ]

        for  (index, beacon) in locate.beacons.enumerated() {
            params[AbstractObject.Request.Beacon.time.getKey(index)] = beacon.time as AnyObject
            params[AbstractObject.Request.Beacon.name.getKey(index)] = beacon.name as AnyObject
            params[AbstractObject.Request.Beacon.level.getKey(index)] = beacon.level as AnyObject
        }
//        print(params)
        let url:String = Server.getUrl(api: .locate(action: .beacon))
        request(url, method: .post, params: params, complitionHandler: complitionHandler)
        
//        Alamofire.request(url, method: .post, parameters: params).responseSwiftyJSON { (response) in
//            let locateResult = LocateObject()
//            locateResult.setData(response: response)
//
//            complitionHandler(locateResult)
//        }
    }
    
    // GPS
    func postGeo (locate:LocateObject, complitionHandler:@escaping (LocateObject) -> Void) {

        var params: [String:AnyObject] = [
            AbstractObject.Request.unigueId.rawValue : locate.uniqueId as AnyObject,
        ]
        
        params[AbstractObject.Request.Geo.latitude.getKey()] =  locate.geo!.latitude as AnyObject
        params[AbstractObject.Request.Geo.longitude.getKey()] = locate.geo!.longitude as AnyObject
        
        let url:String = Server.getUrl(api: .locate(action: .geo))
        
        request(url, method: .post, params: params, complitionHandler: complitionHandler)
        
//        print(params);
//
//        Alamofire.request(url, method: .post, parameters: params).responseSwiftyJSON { (response) in
//
//            let locateResult = LocateObject()
//            locateResult.setData(response: response)
//
//            complitionHandler(locateResult)
//        }
    }
    
    override func createObject() -> AbstractObject {
        return LocateObject()
    }
    
}
