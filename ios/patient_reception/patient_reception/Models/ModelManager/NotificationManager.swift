//
//  NotificationManager.swift
//  patient_reception
//
//  Created by nishimura on 2018/09/27.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit
import Alamofire
import Alamofire_SwiftyJSON
import SwiftyJSON

class NotificationManager: AbstractManager<NotificationObject> {
    
    // 既読処理
    func postRead (notification:NotificationObject, complitionHandler:@escaping (NotificationObject) -> Void) {
        let params: [String:AnyObject] = [
            AbstractObject.Request.unigueId.rawValue : notification.uniqueId as AnyObject,
            ]
        let url:String = Server.getUrl(api: .notification(action: .read))
        request(url, method: .post, params: params, complitionHandler: complitionHandler)
//        Alamofire.request(url, method: .post, parameters: params).responseSwiftyJSON { (response) in
//
//            var notificationResult = NotificationObject()
//            notificationResult.setData(response: response)
//            complitionHandler(notificationResult)
//        }
    }
    
    override func createObject() -> AbstractObject {
        return NotificationObject()
    }
}
